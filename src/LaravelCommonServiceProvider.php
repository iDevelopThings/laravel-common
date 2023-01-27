<?php

namespace IDT\LaravelCommon;

use IDT\LaravelCommon\Lib\DataTransferObject\DtoServiceProvider;
use IDT\LaravelCommon\Lib\HashIds\GetHashIdCommand;
use Illuminate\Support\Facades\App;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCommonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-common')
            ->hasConfigFile([
                'laravel-common',
                'hashids',
            ])
            ->hasCommands([
                GetHashIdCommand::class,
            ]);
    }

    public function packageRegistered()
    {
        $this->app->register(DtoServiceProvider::class);
    }

    public function packageBooted()
    {
        App::mixin(new AppExtension());
    }
}
