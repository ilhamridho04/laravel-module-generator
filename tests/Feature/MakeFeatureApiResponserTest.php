<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Support\Facades\File;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureApiResponserTest extends TestCase
{
    /** @test */
    public function it_creates_api_responser_stub_file()
    {
        // Test that the stub file exists and has correct content
        $stubPath = __DIR__ . '/../../src/stubs/api-responser.trait.stub';

        $this->assertTrue(File::exists($stubPath));

        $stubContent = File::get($stubPath);
        $this->assertStringContainsString('namespace App\Traits;', $stubContent);
        $this->assertStringContainsString('trait ApiResponser', $stubContent);
        $this->assertStringContainsString('protected function successResponse', $stubContent);
        $this->assertStringContainsString('protected function errorResponse', $stubContent);
        $this->assertStringContainsString('protected function validationErrorResponse', $stubContent);
        $this->assertStringContainsString('protected function notFoundResponse', $stubContent);
        $this->assertStringContainsString('protected function unauthorizedResponse', $stubContent);
        $this->assertStringContainsString('protected function forbiddenResponse', $stubContent);
    }

    /** @test */
    public function api_controller_stub_uses_api_responser_trait()
    {
        // Test that the API controller stub contains the ApiResponser trait usage
        $stubPath = __DIR__ . '/../../src/stubs/controller.api.stub';

        $this->assertTrue(File::exists($stubPath));

        $stubContent = File::get($stubPath);
        $this->assertStringContainsString('use App\Traits\ApiResponser;', $stubContent);
        $this->assertStringContainsString('use AuthorizesRequests, ApiResponser;', $stubContent);
        $this->assertStringContainsString('$this->successResponse(', $stubContent);
        $this->assertStringContainsString('{{ model }} list retrieved successfully', $stubContent);
        $this->assertStringContainsString('{{ model }} created successfully', $stubContent);
        $this->assertStringContainsString('{{ model }} retrieved successfully', $stubContent);
        $this->assertStringContainsString('{{ model }} updated successfully', $stubContent);
        $this->assertStringContainsString('{{ model }} deleted successfully', $stubContent);
    }

    /** @test */
    public function api_controller_stub_has_correct_namespace()
    {
        // Test that the API controller stub has the correct namespace
        $stubPath = __DIR__ . '/../../src/stubs/controller.api.stub';

        $stubContent = File::get($stubPath);
        $this->assertStringContainsString('namespace App\Http\Controllers\Api;', $stubContent);
        $this->assertStringContainsString('class {{ model }}Controller extends Controller', $stubContent);
    }
}
