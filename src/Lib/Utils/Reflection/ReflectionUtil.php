<?php

namespace IDT\LaravelCommon\Lib\Utils\Reflection;

use ReflectionProperty;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunctionAbstract;
use ReflectionType;

class ReflectionUtil
{

    public static function getDocComment($type): ?string
    {
        if (method_exists($type, 'getDocComment')) {
            return $type->getDocComment();
        }

        return null;
    }

    public static function getType(ReflectionProperty|ReflectionClass|ReflectionClassConstant|ReflectionFunctionAbstract $type): ?ReflectedType
    {
        return new ReflectedType($type);
    }

    public static function getPropertyTypes($propertyType)
    {
        if ($propertyType instanceof \ReflectionUnionType || $propertyType instanceof \ReflectionIntersectionType) {
            return $propertyType->getTypes();
        }

        return [$propertyType->getType()];
    }


}
