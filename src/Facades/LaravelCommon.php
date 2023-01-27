<?php

namespace IDT\LaravelCommon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IDT\LaravelCommon\LaravelCommon
 */
class LaravelCommon extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \IDT\LaravelCommon\LaravelCommon::class;
    }
}
