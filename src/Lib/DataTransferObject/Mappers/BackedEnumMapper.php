<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mappers;


use BackedEnum;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\DtoMappingHandler;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;
use UnitEnum;

class BackedEnumMapper extends DtoMappingHandler
{
    public function handle(mixed $data): MappingResult
    {
        /** @var BackedEnum $enum */
        $enum = $this->type->getName();

        if (is_a($enum, BackedEnum::class, true)) {
            $value = $enum::tryFrom($data);
            if ($value === null) {
                return $this->failure(new \InvalidArgumentException("Invalid value for enum {$this->type->getName()}"));
            }

            return $this->success($value);
        }

        if (is_a($enum, UnitEnum::class, true)) {
            /** @var UnitEnum $enum */
            $cases = $enum::cases();
            $value = $cases[$data] ?? null;
            if ($value === null) {
                return $this->failure(new \InvalidArgumentException("Invalid value for enum {$this->type->getName()}"));
            }

            return $this->success($value);
        }

        return $this->failure(new \InvalidArgumentException("Failed to resolve enum {$this->type->getName()}"));

    }
}
