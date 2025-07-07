<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureMultiSelectTest extends TestCase
{
    protected Filesystem $files;
    protected string $testModelName = 'MultiSelectTest';
    protected string $testFeaturePlural = 'MultiSelectTests';

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem();
        $this->cleanupTestFiles();
        $this->createDummyRoutes();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestFiles();
        parent::tearDown();
    }

    protected function cleanupTestFiles(): void
    {
        $paths = [
            app_path("Http/Controllers/{$this->testModelName}Controller.php"),
            app_path("Http/Controllers/API/{$this->testModelName}Controller.php"),
            app_path("Http/Requests/Store{$this->testModelName}Request.php"),
            app_path("Http/Requests/Update{$this->testModelName}Request.php"),
            app_path("Models/{$this->testModelName}.php"),
            resource_path("js/pages/{$this->testFeaturePlural}"),
            base_path("routes/Modules/{$this->testFeaturePlural}"),
            database_path("seeders/Permission/{$this->testFeaturePlural}PermissionSeeder.php"),
            app_path("Enums/{$this->testModelName}Status.php"),
            app_path("Policies/{$this->testModelName}Policy.php"),
        ];

        foreach ($paths as $path) {
            if ($this->files->exists($path)) {
                if ($this->files->isDirectory($path)) {
                    $this->files->deleteDirectory($path);
                } else {
                    $this->files->delete($path);
                }
            }
        }
    }

    protected function createDummyRoutes(): void
    {
        // Create dummy routes/modules.php to make system think it's installed
        $modulesContent = "<?php\n// Dummy routes for testing";
        $this->files->ensureDirectoryExists(base_path('routes'));
        $this->files->put(base_path('routes/modules.php'), $modulesContent);

        // Update web.php to include modules.php
        $webContent = "<?php\nrequire __DIR__ . '/modules.php';\n";
        $this->files->put(base_path('routes/web.php'), $webContent);

        // Create dummy api-modules.php 
        $this->files->put(base_path('routes/api-modules.php'), $modulesContent);

        // Update api.php to include api-modules.php
        $apiContent = "<?php\nrequire __DIR__ . '/api-modules.php';\n";
        $this->files->put(base_path('routes/api.php'), $apiContent);
    }

    /** @test */
    public function it_can_create_feature_with_no_optional_components_via_multiselect()
    {
        // Test the new multi-select interface for optional components
        $command = $this->artisan('features:create')
            ->expectsQuestion('📝 Masukkan nama fitur (contoh: Product, UserProfile, Category)', $this->testModelName)
            ->expectsConfirmation('✅ Lanjutkan dengan nama ini?', 'yes')
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '1', // Full-stack
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            )
            ->expectsQuestion('🎯 Masukkan pilihan Anda (default: 0)', '0'); // No optional components

        $command->assertExitCode(0);

        // Should create basic feature without optional components
        $this->assertTrue($this->files->exists(app_path("Models/{$this->testModelName}.php")));
        $this->assertTrue($this->files->exists(app_path("Http/Controllers/{$this->testModelName}Controller.php")));
        
        // Should NOT create optional components
        $this->assertFalse($this->files->exists(app_path("Enums/{$this->testModelName}Status.php")));
        $this->assertFalse($this->files->exists(app_path("Policies/{$this->testModelName}Policy.php")));
    }

    /** @test */
    public function it_can_create_feature_with_multiple_components_via_multiselect()
    {
        // Test selecting multiple components with the new interface
        $command = $this->artisan('features:create')
            ->expectsQuestion('📝 Masukkan nama fitur (contoh: Product, UserProfile, Category)', $this->testModelName)
            ->expectsConfirmation('✅ Lanjutkan dengan nama ini?', 'yes')
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '2', // API Only
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            )
            ->expectsQuestion('🎯 Masukkan pilihan Anda (default: 0)', '1,3'); // Select enum (1) and policy (3)

        $command->assertExitCode(0);

        // Should create basic feature
        $this->assertTrue($this->files->exists(app_path("Models/{$this->testModelName}.php")));
        $this->assertTrue($this->files->exists(app_path("Http/Controllers/{$this->testModelName}Controller.php")));
        
        // Should create selected optional components
        $this->assertTrue($this->files->exists(app_path("Enums/{$this->testModelName}Status.php")));
        $this->assertTrue($this->files->exists(app_path("Policies/{$this->testModelName}Policy.php")));
        
        // Should NOT create non-selected components
        $this->assertFalse($this->files->exists(app_path("Observers/{$this->testModelName}Observer.php")));
    }

    /** @test */
    public function it_can_select_single_component_via_multiselect()
    {
        // Test selecting a single component
        $command = $this->artisan('features:create')
            ->expectsQuestion('📝 Masukkan nama fitur (contoh: Product, UserProfile, Category)', $this->testModelName)
            ->expectsConfirmation('✅ Lanjutkan dengan nama ini?', 'yes')
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '1', // Full-stack
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            )
            ->expectsQuestion('🎯 Masukkan pilihan Anda (default: 0)', '1'); // Select only enum

        $command->assertExitCode(0);

        // Should create basic feature
        $this->assertTrue($this->files->exists(app_path("Models/{$this->testModelName}.php")));
        
        // Should create only selected component
        $this->assertTrue($this->files->exists(app_path("Enums/{$this->testModelName}Status.php")));
        
        // Should NOT create other components
        $this->assertFalse($this->files->exists(app_path("Policies/{$this->testModelName}Policy.php")));
        $this->assertFalse($this->files->exists(app_path("Observers/{$this->testModelName}Observer.php")));
    }
}
