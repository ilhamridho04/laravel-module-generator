<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use NgodingSkuyy\LaravelModuleGenerator\Commands\DeleteFeature;
use NgodingSkuyy\LaravelModuleGenerator\Commands\SetupModulesLoader;
use NgodingSkuyy\LaravelModuleGenerator\Commands\InstallModulesLoader;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_loads_the_service_provider()
    {
        // Check if service provider is loaded
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey('NgodingSkuyy\LaravelModuleGenerator\LaravelModuleGeneratorServiceProvider', $providers);
    }

    /** @test */
    public function it_registers_the_make_feature_command()
    {
        $command = $this->app->make(MakeFeature::class);
        $this->assertInstanceOf(MakeFeature::class, $command);
    }

    /** @test */
    public function make_feature_command_is_available()
    {
        $commands = $this->app[\Illuminate\Contracts\Console\Kernel::class]->all();
        $this->assertArrayHasKey('module:create', $commands);
    }

    /** @test */
    public function make_feature_command_has_correct_signature()
    {
        $command = $this->app->make(MakeFeature::class);

        $this->assertEquals('module:create', $command->getName());
        $this->assertStringContainsString('Generate full CRUD feature', $command->getDescription());
    }

    /** @test */
    public function it_registers_the_delete_feature_command()
    {
        $command = $this->app->make(DeleteFeature::class);
        $this->assertInstanceOf(DeleteFeature::class, $command);
    }

    /** @test */
    public function delete_feature_command_is_available()
    {
        $commands = $this->app[\Illuminate\Contracts\Console\Kernel::class]->all();
        $this->assertArrayHasKey('module:delete', $commands);
    }

    /** @test */
    public function delete_feature_command_has_correct_signature()
    {
        $command = $this->app->make(DeleteFeature::class);

        $this->assertEquals('module:delete', $command->getName());
        $this->assertStringContainsString('Delete full CRUD feature', $command->getDescription());
    }

    /** @test */
    public function it_registers_the_setup_modules_loader_command()
    {
        $command = $this->app->make(SetupModulesLoader::class);
        $this->assertInstanceOf(SetupModulesLoader::class, $command);
    }

    /** @test */
    public function setup_modules_loader_command_is_available()
    {
        $commands = $this->app[\Illuminate\Contracts\Console\Kernel::class]->all();
        $this->assertArrayHasKey('module:setup', $commands);
    }

    /** @test */
    public function it_registers_the_install_modules_loader_command()
    {
        $command = $this->app->make(InstallModulesLoader::class);
        $this->assertInstanceOf(InstallModulesLoader::class, $command);
    }

    /** @test */
    public function install_modules_loader_command_is_available()
    {
        $commands = $this->app[\Illuminate\Contracts\Console\Kernel::class]->all();
        $this->assertArrayHasKey('module:install', $commands);
    }
}
