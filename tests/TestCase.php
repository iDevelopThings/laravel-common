<?php

namespace IDT\LaravelCommon\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use IDT\LaravelCommon\LaravelCommonServiceProvider;
use Vinkla\Hashids\HashidsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'IDT\\LaravelCommon\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            HashidsServiceProvider::class,
            LaravelCommonServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('hashids.default', 'main');
        config()->set('hashids.connections.main', [
            'salt'     => 'WLiE4kwLZXsvXwuUbkI3i2wMmlfkMgkT',
            'length'   => 16,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyz1234567890',
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-common_table.php.stub';
        $migration->up();
        */
    }
}
