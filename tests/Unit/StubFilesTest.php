<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use Illuminate\Support\Facades\File;

class StubFilesTest extends TestCase
{
    /** @test */
    public function all_required_stub_files_exist()
    {
        $stubPath = __DIR__ . '/../../src/stubs';
        $viewsPath = __DIR__ . '/../../src/views';

        $requiredStubs = [
            'model.stub',
            'controller.stub',
            'request.store.stub',
            'request.update.stub',
            'migration.stub',
            'routes.stub',
            'seeder.permission.stub',
            'Enum.stub',
            'Observer.stub',
        ];

        $requiredViews = [
            'Index.vue.stub',
            'Create.vue.stub',
            'Edit.vue.stub',
            'Show.vue.stub',
        ];

        foreach ($requiredStubs as $stub) {
            $stubFile = $stubPath . '/' . $stub;
            $this->assertFileExists($stubFile, "Stub file {$stub} should exist");
        }

        foreach ($requiredViews as $view) {
            $viewFile = $viewsPath . '/' . $view;
            $this->assertFileExists($viewFile, "View file {$view} should exist");
        }
    }

    /** @test */
    public function model_stub_contains_required_placeholders()
    {
        $stubPath = __DIR__ . '/../../src/stubs/model.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for placeholder variables with spaces
        $this->assertStringContainsString('{{ model }}', $content);
        $this->assertStringContainsString('{{ table }}', $content);
    }

    /** @test */
    public function controller_stub_contains_required_placeholders()
    {
        $stubPath = __DIR__ . '/../../src/stubs/controller.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for placeholder variables with spaces
        $this->assertStringContainsString('{{ model }}', $content);
        $this->assertStringContainsString('{{ plural }}', $content);
    }

    /** @test */
    public function vue_stubs_contain_required_structure()
    {
        $vueStubs = ['Index.vue.stub', 'Create.vue.stub', 'Edit.vue.stub', 'Show.vue.stub'];

        foreach ($vueStubs as $stub) {
            $stubPath = __DIR__ . '/../../src/views/' . $stub;
            $this->assertFileExists($stubPath);

            $content = file_get_contents($stubPath);

            // Check for Vue 3 Composition API structure
            $this->assertStringContainsString('<template>', $content);
            // Check for either JavaScript or TypeScript script setup
            $this->assertTrue(
                str_contains($content, '<script setup>') || str_contains($content, '<script setup lang="ts">'),
                "Vue component should contain either '<script setup>' or '<script setup lang=\"ts\">'"
            );

            // Check for at least one placeholder variable
            $hasPlaceholder = str_contains($content, '{{ table }}') ||
                str_contains($content, '{{ model }}') ||
                str_contains($content, '{{ plural }}');
            $this->assertTrue($hasPlaceholder, "Vue stub {$stub} should contain at least one placeholder");
        }
    }

    /** @test */
    public function migration_stub_contains_required_structure()
    {
        $stubPath = __DIR__ . '/../../src/stubs/migration.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for Laravel migration structure
        $this->assertStringContainsString('use Illuminate\Database\Migrations\Migration', $content);
        $this->assertStringContainsString('Schema::create', $content);
        $this->assertStringContainsString('Schema::dropIfExists', $content);
        $this->assertStringContainsString('{{ table }}', $content);
    }

    /** @test */
    public function request_stubs_contain_required_structure()
    {
        $requestStubs = ['request.store.stub', 'request.update.stub'];

        foreach ($requestStubs as $stub) {
            $stubPath = __DIR__ . '/../../src/stubs/' . $stub;
            $this->assertFileExists($stubPath);

            $content = file_get_contents($stubPath);

            // Check for Laravel FormRequest structure
            $this->assertStringContainsString('use Illuminate\Foundation\Http\FormRequest', $content);
            $this->assertStringContainsString('public function authorize()', $content);
            $this->assertStringContainsString('public function rules()', $content);
            $this->assertStringContainsString('{{ model }}', $content);
        }
    }

    /** @test */
    public function permission_seeder_stub_contains_required_structure()
    {
        $stubPath = __DIR__ . '/../../src/stubs/seeder.permission.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for Laravel Seeder structure
        $this->assertStringContainsString('use Illuminate\Database\Seeder', $content);
        $this->assertStringContainsString('public function run()', $content);
        $this->assertStringContainsString('Permission::firstOrCreate', $content);
        $this->assertStringContainsString('{{ permission }}', $content);
    }

    /** @test */
    public function routes_stub_contains_required_structure()
    {
        $stubPath = __DIR__ . '/../../src/stubs/routes.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for Laravel routes structure
        $this->assertStringContainsString('Route::', $content);
        $this->assertStringContainsString('{{ controller }}', $content);
        $this->assertStringContainsString('{{ kebab }}', $content);
    }

    /** @test */
    public function enum_stub_contains_required_structure()
    {
        $stubPath = __DIR__ . '/../../src/stubs/Enum.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for PHP 8.1+ enum structure
        $this->assertStringContainsString('enum ', $content);
        $this->assertStringContainsString('{{ model }}', $content);
    }

    /** @test */
    public function observer_stub_contains_required_structure()
    {
        $stubPath = __DIR__ . '/../../src/stubs/Observer.stub';
        $this->assertFileExists($stubPath);

        $content = file_get_contents($stubPath);

        // Check for Laravel Observer structure
        $this->assertStringContainsString('class {{ model }}Observer', $content);
        $this->assertStringContainsString('public function creating(', $content);
        $this->assertStringContainsString('public function updating(', $content);
        $this->assertStringContainsString('public function deleting(', $content);
        $this->assertStringContainsString('{{ model }}', $content);
    }
}
