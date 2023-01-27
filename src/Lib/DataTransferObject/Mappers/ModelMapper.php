<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mappers;


use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\DtoMappingHandler;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;
use Illuminate\Database\Eloquent\Model;

class ModelMapper extends DtoMappingHandler
{
    public function handle(mixed $data): MappingResult
    {
        /** @var Model $fqn */
        $fqn = $this->type->getName();

        if (method_exists($fqn, 'findOrFailByHashId')) {
            return $this->success($fqn::findOrFailByHashId($data));
        }


        return $this->success($fqn::find($data));
    }
}
