<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
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
    }    /** @test */
    public function it_can_create_feature_with_no_optional_components()
    {
        // Test creating feature without optional components using flags
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
            '--skip-install' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create basic feature without optional components
        $this->assertTrue($this->files->exists(app_path("Models/{$this->testModelName}.php")));
        $this->assertTrue($this->files->exists(app_path("Http/Controllers/{$this->testModelName}Controller.php")));

        // Should NOT create optional components by default
        $this->assertFalse($this->files->exists(app_path("Enums/{$this->testModelName}Status.php")));
        $this->assertFalse($this->files->exists(app_path("Policies/{$this->testModelName}Policy.php")));
    }

    /** @test */
    public function it_can_create_feature_with_multiple_components_via_flags()
    {
        // Test selecting multiple components using --with flag
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
            '--api' => true,
            '--with' => ['enum', 'policy'],
            '--skip-install' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create basic feature
        $this->assertTrue($this->files->exists(app_path("Models/{$this->testModelName}.php")));
        $this->assertTrue($this->files->exists(app_path("Http/Controllers/API/{$this->testModelName}Controller.php")));

        // Should create selected optional components
        $this->assertTrue($this->files->exists(app_path("Enums/{$this->testModelName}Status.php")));
        $this->assertTrue($this->files->exists(app_path("Policies/{$this->testModelName}Policy.php")));

        // Should NOT create non-selected components
        $this->assertFalse($this->files->exists(app_path("Observers/{$this->testModelName}Observer.php")));
    }

    /** @test */
    public function it_can_select_single_component_via_flag()
    {
        // Test selecting a single component
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
            '--with' => ['enum'],
            '--skip-install' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create basic feature
        $this->assertTrue($this->files->exists(app_path("Models/{$this->testModelName}.php")));

        // Should create only selected component
        $this->assertTrue($this->files->exists(app_path("Enums/{$this->testModelName}Status.php")));

        // Should NOT create other components
        $this->assertFalse($this->files->exists(app_path("Policies/{$this->testModelName}Policy.php")));
        $this->assertFalse($this->files->exists(app_path("Observers/{$this->testModelName}Observer.php")));
    }
}
