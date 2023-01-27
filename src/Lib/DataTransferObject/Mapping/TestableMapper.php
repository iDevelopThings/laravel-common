<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mapping;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

interface TestableMapper
{
    /**
     * @param ReflectionProperty  $property
     * @param ReflectionNamedType|ReflectionClass $type
     *
     * Return a tuple of [canMap, Mapper FQN]
     *
     * @return array{bool, string}
     */
    public static function canMap(ReflectionProperty $property, ReflectionNamedType|ReflectionClass $type): array;
}
