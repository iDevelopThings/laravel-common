<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mappers;


use BackedEnum;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\DtoMappingHandler;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;

class BackedEnumMapper extends DtoMappingHandler
{
    public function handle(mixed $data): MappingResult
    {
        /** @var BackedEnum $enum */
        $enum = $this->type->getName();

        $value = $enum::tryFrom($data);
        if($value === null) {
            return $this->failure(new \InvalidArgumentException("Invalid value for enum {$this->type->getName()}"));
        }

        return $this->success($value);
    }
}
