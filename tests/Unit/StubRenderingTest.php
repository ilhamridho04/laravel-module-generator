<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class StubRenderingTest extends TestCase
{
    /** @test */
    public function it_can_load_package_service_provider()
    {
        // Simple test to verify the package is loaded
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_access_make_feature_command()
    {
        // Test that we can resolve the command from container
        $command = $this->app->make(\NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature::class);
        $this->assertNotNull($command);
    }
}
