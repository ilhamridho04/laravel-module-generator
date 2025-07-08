<?php

namespace NgodingSkuyy\LaravelModuleGenerator;

use Illuminate\Support\ServiceProvider;
use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use NgodingSkuyy\LaravelModuleGenerator\Commands\DeleteFeature;
use NgodingSkuyy\LaravelModuleGenerator\Commands\SetupModulesLoader;
use NgodingSkuyy\LaravelModuleGenerator\Commands\InstallModulesLoader;

class LaravelModuleGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFeature::class,
                DeleteFeature::class,
                SetupModulesLoader::class,
                InstallModulesLoader::class,
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
