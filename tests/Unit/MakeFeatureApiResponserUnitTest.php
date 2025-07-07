<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureApiResponserUnitTest extends TestCase
{
    /** @test */
    public function determineGenerationMode_returns_correct_values()
    {
        $command = new MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('determineGenerationMode');
        $method->setAccessible(true);

        // Test API only mode
        $result = $method->invoke($command, true, false);
        $this->assertEquals('API Only', $result);

        // Test View only mode
        $result = $method->invoke($command, false, true);
        $this->assertEquals('View Only', $result);

        // Test Full-stack mode
        $result = $method->invoke($command, false, false);
        $this->assertEquals('Full-stack (API + View)', $result);
    }

    /** @test */
    public function getBasicStub_returns_correct_api_controller_stub()
    {
        $command = new MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getBasicStub');
        $method->setAccessible(true);

        // Test API controller stub
        $result = $method->invoke($command, 'controller.api.stub');

        $this->assertStringContainsString('namespace App\\Http\\Controllers\\API;', $result);
        $this->assertStringContainsString('use App\\Models\\{{ model }};', $result);
        $this->assertStringContainsString('class {{ model }}Controller extends Controller', $result);
        $this->assertStringContainsString('public function index()', $result);
        $this->assertStringContainsString('public function store(', $result);
        $this->assertStringContainsString('public function show(', $result);
        $this->assertStringContainsString('public function update(', $result);
        $this->assertStringContainsString('public function destroy(', $result);
    }

    /** @test */
    public function getBasicStub_returns_correct_view_controller_stub()
    {
        $command = new MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getBasicStub');
        $method->setAccessible(true);

        // Test View controller stub
        $result = $method->invoke($command, 'controller.view.stub');

        $this->assertStringContainsString('namespace App\\Http\\Controllers;', $result);
        $this->assertStringContainsString('use Inertia\\Inertia;', $result);
        $this->assertStringContainsString('Inertia::render(', $result);
        $this->assertStringContainsString('redirect()->route(', $result);
    }

    /** @test */
    public function getBasicStub_returns_api_routes_stub()
    {
        $command = new MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getBasicStub');
        $method->setAccessible(true);

        // Test API routes stub
        $result = $method->invoke($command, 'routes.api.stub');

        $this->assertStringContainsString('use App\\Http\\Controllers\\API\\{{ model }}Controller;', $result);
        $this->assertStringContainsString('Route::middleware([\'auth:sanctum\'])', $result);
        $this->assertStringContainsString('Route::apiResource(', $result);
    }
}
