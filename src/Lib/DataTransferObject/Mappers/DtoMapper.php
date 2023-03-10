<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mappers;


use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\DtoMappingHandler;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;
use IDT\LaravelCommon\Lib\DataTransferObject\MappingManager;
use Illuminate\Http\Request;

class DtoMapper extends DtoMappingHandler
{
    public function handle(mixed $data): MappingResult
    {
        if (empty($data) && $this->type->allowsNull()) {
            return $this->resolved(null);
        }

        $name = $this->type->getName();

        return $this->resolved(
            MappingManager::for($name)->fillWithAttributes($data, false)
        );
    }
}
