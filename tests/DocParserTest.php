<?php


use IDT\LaravelCommon\Lib\Utils\Reflection\ReflectionUtil;
use IDT\LaravelCommon\Tests\Fixtures\Dto\ComplexDto;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\ContextFactory;

it('can parse array info from doc block', function () {
    $parser = DocBlockFactory::createInstance();
    // $context = (new ContextFactory())->createFromReflector($this->type);

    $docs = [
        '/** @var array<string, int> */',
        '/** @var array<int> */',
        '/** @var array{0: int} */',
        '/** @var array{0: int, 1: string} */',
        '/** @var \Illuminate\Support\Collection<string, int> */',
        '/** @var \Illuminate\Support\Collection */',
    ];

    $results = [];
    foreach ($docs as $doc) {
        $block = $parser->create($doc);
        $tags  = $block->getTags();

        $r = $block->getTagsWithTypeByName('var');

        foreach ($tags as $tag) {


            if ($tag instanceof Var_) {
                $type = $tag->getType();
                if ($type instanceof AbstractList) {
                    if ($type instanceof Collection) {
                        $result = [
                            'type'  => 'collection',
                            'fqn'   => (string)$type->getFqsen(),
                            'key'   => (string)$type->getKeyType(),
                            'value' => (string)$type->getValueType(),
                        ];

                        $results[] = $result;
                        continue;
                    }
                    $result = [
                        'type'  => 'array',
                        'key'   => (string)$type->getKeyType(),
                        'value' => (string)$type->getValueType(),
                    ];

                    $results[] = $result;
                    continue;
                }

                if ($type instanceof \phpDocumentor\Reflection\Types\Object_) {
                    $result = [
                        'type'  => 'object',
                        'fqn'   => (string)$type->getFqsen(),
                        'class' => $type->getFqsen()->getName(),
                    ];

                    $results[] = $result;

                    continue;

                }

                $result = [
                    'type'  => 'scalar',
                    'class' => (string)$type,
                ];

                $results[] = $result;

                continue;
            }

            if ($tag instanceof InvalidTag) {
                if (str_contains((string)$tag, 'array')) {
                    $results[] = [
                        'type'  => 'array',
                        'key'   => 'string|int',
                        'value' => 'mixed',
                    ];
                    continue;
                }

                $results[] = [
                    'type'  => 'invalid',
                    'class' => (string)$tag,
                ];
                continue;
            }
            $results[] = [
                'type'  => 'unknown',
                'class' => (string)$tag,
            ];

        }

    }

    expect($results)->not()->toBeEmpty();

});

it('can parse doc blocks for type', function () {

    $cType = new ReflectionClass(ComplexDto::class);

    $types = [];

    foreach ($cType->getProperties() as $property) {
        $rType = ReflectionUtil::getType($property);

        $types[$property->getName()] = $rType->resolveType();
    }

    expect($types)->not()->toBeEmpty();

});
