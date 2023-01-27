<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mapping;

use IDT\LaravelCommon\Lib\DataTransferObject\DataTransferObject;
use IDT\LaravelCommon\Lib\DataTransferObject\MappingManager;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

abstract class DtoMappingHandler
{
    public function __construct(
        protected DataTransferObject $dto,
        protected MappingManager $mappingManager,
        protected ReflectionNamedType|ReflectionClass $type,
        protected ReflectionProperty $property,
        protected ?Request $request = null,
    ) {

    }

    public abstract function handle(mixed $data): MappingResult;

    protected function noResult(): MappingResult
    {
        return MappingResult::noResult();
    }

    protected function success(mixed $value): MappingResult
    {
        return MappingResult::success($value);
    }

    protected function resolved(mixed $value): MappingResult
    {
        return MappingResult::success($value);
    }

    protected function failure(\Throwable $exception): MappingResult
    {
        return MappingResult::failure($exception);
    }
}
