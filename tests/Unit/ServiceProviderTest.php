<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
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
        $this->assertArrayHasKey('make:feature', $commands);
    }

    /** @test */
    public function make_feature_command_has_correct_signature()
    {
        $command = $this->app->make(MakeFeature::class);

        $this->assertEquals('make:feature', $command->getName());
        $this->assertStringContainsString('Generate full CRUD feature', $command->getDescription());
    }
}
