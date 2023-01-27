<?php

namespace IDT\LaravelCommon\Tests\Fixtures\Dto;

use IDT\LaravelCommon\Lib\DataTransferObject\DataTransferObject;
use Illuminate\Support\Collection;

class ComplexDto extends DataTransferObject
{
    public ?BasicDto $basicDto = null;

    /** @var BasicDto[] */
    public ?array $basicDtoArray = [];

    /** @var Collection<BasicDto> */
    public $basicDtoCollection = null;
}
