<?php

namespace IDT\LaravelCommon\Lib\Utils\Reflection\Annotations;

use ReflectionClass;

class TypeFromAnnotation
{

    public function __construct(
        public ?string $fqn,

        public ?string $group,

        public ?string $typeName,
        public ?string $keyType = null,
        public ?string $valueType = null,
    ) {

    }

    public function isArrayType()
    {
        return $this->typeName === 'array' && $this->keyType && $this->valueType && !$this->fqn;
    }

    public function hasFqn()
    {
        return str_starts_with($this->fqn ?? '', '\\');
    }

    public function hasFqnValueType()
    {
        return str_starts_with($this->valueType, '\\');
    }

    public function getMainType(): ?ReflectionClass
    {
        if (!$this->fqn) {
            return null;
        }

        return new ReflectionClass($this->fqn);
    }

}
