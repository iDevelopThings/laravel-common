<?php

namespace IDT\LaravelCommon\Lib\Utils\Reflection\Annotations;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlockFactory;

class ReflectionAnnotationList
{
    private static DocBlockFactory $parser;

    /** @var Collection<ReflectionAnnotationData> $annotations */
    public Collection $annotations;

    /** @var Collection<ReflectionAnnotationData>|null $var */
    private ?Collection $var = null;

    /** @var Collection<ReflectionAnnotationData>|null $return */
    private ?Collection $return = null;

    /** @var Collection<ReflectionAnnotationData>|null $method */
    private ?Collection $method = null;

    /** @var Collection<ReflectionAnnotationData>|null $param */
    private ?Collection $param = null;

    /**
     * @var ReflectionAnnotationData[] $annotations
     */
    public function __construct(array $annotations)
    {
        $this->annotations = collect($annotations);
    }

    public static function process(string $doc)
    {
        $parser = self::$parser ??= DocBlockFactory::createInstance();
        $contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();

        $docBlock = $parser->create($doc);
    }

    public function getVars()
    {
        return $this->var ??= $this->allWithAnnotation('var');
    }

    public function getReturns()
    {
        return $this->return ??= $this->allWithAnnotation('return');
    }

    public function getMethods()
    {
        return $this->method ??= $this->allWithAnnotation('method');
    }

    public function getParams()
    {
        return $this->param ??= $this->allWithAnnotation('param');
    }

    public function getVar()
    {
        return $this->getVars()->first();
    }

    public function getReturn()
    {
        return $this->getReturns()->first();
    }

    public function getMethod()
    {
        return $this->getMethods()->first();
    }

    public function getParam()
    {
        return $this->getParams()->first();
    }

    public function allWithAnnotation(string $string)
    {
        return $this->annotations->filter(fn(ReflectionAnnotationData $annotation) => $annotation->annotationType === $string);
    }


    public function isEmpty(): bool
    {
        return $this->annotations->isEmpty();
    }


}
