<?php

namespace IDT\LaravelCommon\Lib\Menu;

use IDT\LaravelCommon\Lib\DataTransferObject\DataTransferObject;

class RouteInfo extends DataTransferObject
{
	public ?string $name       = null;
	public array   $parameters = [];
}
