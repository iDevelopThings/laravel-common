<?php

namespace IDT\LaravelCommon\Lib\Utils\Reflection;

use IDT\LaravelCommon\Lib\Utils\Reflection\Annotations\ReflectionAnnotationData;
use IDT\LaravelCommon\Lib\Utils\Reflection\Annotations\ReflectionAnnotationList;
use IDT\LaravelCommon\Lib\Utils\Reflection\Annotations\TypeFromAnnotation;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use PHPStan\Rules\Api\PhpStanNamespaceIn3rdPartyPackageRule;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

class ReflectedType
{
    private static DocBlockFactory $annotationParser;

    public ReflectionProperty|ReflectionClass|ReflectionClassConstant|ReflectionFunctionAbstract $type;

    private ?TypeFromAnnotation $resolvedType = null;

    private ?DocBlock $annotations = null;

    private string|null $comment = null;

    public function __construct(ReflectionProperty|ReflectionClass|ReflectionClassConstant|ReflectionFunctionAbstract $base)
    {
        $this->type = $base;
    }

    public function getDocComment(): ?string
    {
        return $this->comment ??=
            (method_exists($this->type, 'getDocComment') ? $this->type->getDocComment() : null);
    }

    public function getAnnotations(): ?DocBlock
    {
        if ($this->annotations) {
            return $this->annotations;
        }

        $doc = $this->getDocComment();
        if (!$doc) {
            return null;
        }

        $parser  = self::getDocParser();
        $context = (new ContextFactory())->createFromReflector($this->type);

        return $parser->create($doc, $context);
    }

    public function getName()
    {
        return $this->type->getName();
    }

    public function isBuiltin()
    {
        return $this->type->isBuiltin();
    }

    public function allowsNull()
    {
        return $this->type->allowsNull();
    }

    public function __toString()
    {
        return $this->type->__toString();
    }

    public function getPhpTypes()
    {
        $type = $this->type->getType();

        if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
            return $type->getTypes();
        } elseif ($type instanceof ReflectionNamedType) {
            return [$type];
        }

        return [null];
    }

    public function getPhpType(): ?ReflectionNamedType
    {
        return head($this->getPhpTypes());
    }

    public function resolveType()
    {
        if ($this->resolvedType) {
            return $this->resolvedType;
        }

        $type = $this->getPhpType();
        if ($type !== null && $type->getName() !== 'array') {
            return new TypeFromAnnotation(
                fqn: $type->getName(),
                group: $type->isBuiltin() ? 'builtin' : 'php',
                typeName: (string)$type,
            );
        }

        $annotations = $this->getAnnotations();
        if (!$annotations) {
            return null;
        }

        if ($this->type instanceof ReflectionMethod) {
            $tags = $annotations->getTagsWithTypeByName('return');
        } else {
            $tags = $annotations->getTagsWithTypeByName('var');
        }
        if (empty($tags)) {
            return null;
        }

        return self::resolveTypeFromTag($tags[0]);
    }

    public static function resolveTypeFromTag(TagWithType $tag)
    {
        $type = $tag->getType();

        if ($type instanceof InvalidTag) {
            if (str_contains((string)$tag, 'array')) {
                return new TypeFromAnnotation(
                    fqn: null,
                    group: 'array',
                    typeName: 'array',
                    keyType: 'string|int',
                    valueType: 'mixed',
                );
            }

            return new TypeFromAnnotation(
                fqn: null,
                group: 'invalid',
                typeName: (string)$type,
                keyType: 'string|int',
                valueType: 'mixed',
            );
        }

        if ($type instanceof AbstractList) {
            $result = new TypeFromAnnotation(
                fqn: null,
                group: 'array',
                typeName: 'array',
                keyType: (string)$type->getKeyType(),
                valueType: (string)$type->getValueType(),
            );

            if ($type instanceof Collection) {
                $result->fqn      = (string)$type->getFqsen();
                $result->group    = 'collection';
                $result->typeName = $type->getFqsen()->getName();
            }

            return $result;
        }

        if ($type instanceof Object_) {
            return new TypeFromAnnotation(
                fqn: (string)$type->getFqsen(),
                group: 'object',
                typeName: $type->getFqsen()->getName(),
            );
        }

        return new TypeFromAnnotation(
            fqn: null,
            group: 'scalar',
            typeName: (string)$type,
        );
    }

    public function resolveTypeToPhpType(): ?ReflectionClass
    {
        $type = $this->resolveType();
        if (!$type) {
            return null;
        }
        $refType = $type->getMainType();
        if ($refType) {
            return $refType;
        }

        if ($type->group === 'array') {
            return new ReflectionClass('array');
        }

        if ($type->group === 'object') {
            return new ReflectionClass($type->fqn);
        }

        return null;
    }

    public static function getDocParser()
    {
        return self::$annotationParser ??= DocBlockFactory::createInstance();
    }

}
