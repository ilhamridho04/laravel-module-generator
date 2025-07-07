<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Support\Facades\File;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureRoutesSeparationTest extends TestCase
{
    /** @test */
    public function it_creates_separate_web_and_api_modules_loaders()
    {
        // Test that both loaders have correct content
        $webLoaderStub = __DIR__ . '/../../src/stubs/modules-loader.stub';
        $apiLoaderStub = __DIR__ . '/../../src/stubs/api-modules-loader.stub';

        $this->assertTrue(File::exists($webLoaderStub));
        $this->assertTrue(File::exists($apiLoaderStub));

        $webContent = File::get($webLoaderStub);
        $apiContent = File::get($apiLoaderStub);

        // Web loader should handle web routes
        $this->assertStringContainsString('Web Modules Auto-loader', $webContent);
        $this->assertStringContainsString('web.php', $webContent);
        $this->assertStringContainsString("middleware('web')", $webContent);
        $this->assertStringNotContainsString('api.php', $webContent);

        // API loader should handle API routes
        $this->assertStringContainsString('API Modules Auto-loader', $apiContent);
        $this->assertStringContainsString('api.php', $apiContent);
        $this->assertStringContainsString("middleware('api')", $apiContent);
        $this->assertStringContainsString("prefix('api')", $apiContent);
        $this->assertStringNotContainsString('web.php', $apiContent);
    }

    /** @test */
    public function api_routes_stub_uses_correct_controller_reference()
    {
        $apiRoutesStub = __DIR__ . '/../../src/stubs/routes.api.stub';

        $this->assertTrue(File::exists($apiRoutesStub));

        $content = File::get($apiRoutesStub);

        // Should use model parameter, not controller
        $this->assertStringContainsString('use App\Http\Controllers\API\{{ model }}Controller;', $content);
        $this->assertStringContainsString('Route::apiResource(\'{{ kebab }}\', {{ model }}Controller::class);', $content);

        // Should have auth:sanctum middleware
        $this->assertStringContainsString("middleware(['auth:sanctum'])", $content);

        // Should have comments about automatic prefixing
        $this->assertStringContainsString('API routes are automatically prefixed', $content);
    }

    /** @test */
    public function web_routes_stub_exists_and_has_correct_content()
    {
        $webRoutesStub = __DIR__ . '/../../src/stubs/routes.stub';

        $this->assertTrue(File::exists($webRoutesStub));

        $content = File::get($webRoutesStub);

        // Should use regular controller namespace
        $this->assertStringContainsString('use App\Http\Controllers\{{ model }}Controller;', $content);
        $this->assertStringContainsString('Route::resource(\'{{ kebab }}\', {{ model }}Controller::class);', $content);
    }

    /** @test */
    public function view_only_routes_stub_has_auth_middleware()
    {
        $viewRoutesStub = __DIR__ . '/../../src/stubs/routes.view.stub';

        $this->assertTrue(File::exists($viewRoutesStub));

        $content = File::get($viewRoutesStub);

        // Should have auth middleware for view routes
        $this->assertStringContainsString("middleware(['auth', 'verified'])", $content);
        $this->assertStringContainsString('use App\Http\Controllers\{{ model }}Controller;', $content);
    }

    /** @test */
    public function getBasicStub_returns_correct_api_routes()
    {
        $command = new \NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getBasicStub');
        $method->setAccessible(true);

        // Test API routes stub
        $result = $method->invoke($command, 'routes.api.stub');

        $this->assertStringContainsString('use App\\Http\\Controllers\\API\\{{ model }}Controller;', $result);
        $this->assertStringContainsString('Route::middleware([\'auth:sanctum\'])', $result);
        $this->assertStringContainsString('Route::apiResource(', $result);
        $this->assertStringContainsString('{{ kebab }}', $result);
        $this->assertStringContainsString('{{ model }}Controller::class', $result);
    }

    /** @test */
    public function getBasicStub_returns_correct_web_routes()
    {
        $command = new \NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getBasicStub');
        $method->setAccessible(true);

        // Test web routes stub
        $result = $method->invoke($command, 'routes.stub');

        $this->assertStringContainsString('use App\\Http\\Controllers\\{{ model }}Controller;', $result);
        $this->assertStringContainsString('Route::resource(', $result);
        $this->assertStringContainsString('{{ kebab }}', $result);
        $this->assertStringContainsString('{{ model }}Controller::class', $result);
    }

    /** @test */
    public function getBasicStub_returns_correct_view_routes()
    {
        $command = new \NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getBasicStub');
        $method->setAccessible(true);

        // Test view routes stub
        $result = $method->invoke($command, 'routes.view.stub');

        $this->assertStringContainsString('use App\\Http\\Controllers\\{{ model }}Controller;', $result);
        $this->assertStringContainsString('Route::middleware([\'auth\', \'verified\'])', $result);
        $this->assertStringContainsString('Route::resource(', $result);
        $this->assertStringContainsString('{{ kebab }}', $result);
        $this->assertStringContainsString('{{ model }}Controller::class', $result);
    }
}
