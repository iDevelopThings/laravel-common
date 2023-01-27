<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mappers\Builtin;


use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\DtoMappingHandler;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\TestableMapper;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionClass;

class BoolMapper extends DtoMappingHandler implements TestableMapper
{
    public function handle(mixed $data): MappingResult
    {
        return $this->success((bool)$data);
    }

    public static function canMap(ReflectionProperty $property, ReflectionNamedType|ReflectionClass $type): array
    {
        if($type instanceof ReflectionClass) {
            return [false, null];
        }

        return [$type->isBuiltin() && $type->getName() === 'bool', null];
    }
}
