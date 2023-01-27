<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject;

interface DtoEvents
{
    public function beforeFilling(): void;

    public function afterFilling(): void;

}
