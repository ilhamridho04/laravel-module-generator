<?php

namespace NgodingSkuyy\LaravelModuleGenerator;

use Illuminate\Support\ServiceProvider;
use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;

class LaravelModuleGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeFeature::class,
            ]);

            // Publish stubs
            $this->publishes([
                __DIR__ . '/stubs' => base_path('stubs/laravel-module-generator'),
            ], 'laravel-module-generator-stubs');
        }
    }

    public function register()
    {
        //
    }
}
