<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests;

use NgodingSkuyy\LaravelModuleGenerator\LaravelModuleGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelModuleGeneratorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up Laravel 12+ specific configurations
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }
}
