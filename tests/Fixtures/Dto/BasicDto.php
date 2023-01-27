<?php

namespace IDT\LaravelCommon\Tests\Fixtures\Dto;

use IDT\LaravelCommon\Lib\DataTransferObject\DataTransferObject;

class BasicDto extends DataTransferObject
{
    public ?int    $intValue    = null;
    public ?float  $floatValue  = null;
    public ?bool   $boolValue   = null;
    public ?string $stringValue = null;
    /** @var int[] */
    public ?array $arrayValue = null;
}
