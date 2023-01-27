<?php

namespace IDT\LaravelCommon\Lib\Menu;
use App\Support\DataTransferObject;

class RouteInfo extends DataTransferObject
{
	public ?string $name       = null;
	public array   $parameters = [];
}
