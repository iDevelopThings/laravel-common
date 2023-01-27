<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject;

use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResolver;
use Illuminate\Support\ServiceProvider;

class DtoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MappingResolver::class, function ($app) {
            return new MappingResolver(config('common.dto.mappers'));
        });
    }

    public function boot()
    {
        $this->app->resolving(DataTransferObject::class, function (DataTransferObject $dto, $app) {
            if ($request = $app['request']) {
                return MappingManager::for(get_class($dto))->fillWithRequestData($request, true, $dto);
            }

            return $dto;
        });
    }
}
