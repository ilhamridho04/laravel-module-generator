<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureInteractiveTest extends TestCase
{
    protected Filesystem $files;
    protected string $testModelName = 'InteractiveTest';
    protected string $testFeaturePlural = 'InteractiveTests';

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem();
        $this->cleanupTestFiles();

        // Create dummy routes files to avoid auto-install detection
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
            app_path("Http/Requests/Store{$this->testModelName}Request.php"),
            app_path("Http/Requests/Update{$this->testModelName}Request.php"),
            app_path("Models/{$this->testModelName}.php"),
            resource_path("js/pages/{$this->testFeaturePlural}"),
            base_path("routes/Modules/{$this->testFeaturePlural}"),
            database_path("seeders/Permission/{$this->testFeaturePlural}PermissionSeeder.php"),
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
    public function it_shows_interactive_menu_when_no_mode_specified()
    {
        // Test that when running without --api or --view, it shows the interactive menu
        $command = $this->artisan('features:create', ['name' => $this->testModelName])
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '1',
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            );

        $command->assertExitCode(0);

        // Should create full-stack feature
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $viewsPath = resource_path("js/pages/{$this->testFeaturePlural}");
        $this->assertTrue($this->files->exists($viewsPath));
    }

    /** @test */
    public function it_can_select_api_only_mode_interactively()
    {
        $command = $this->artisan('features:create', ['name' => $this->testModelName])
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '2', // API Only
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            );

        $command->assertExitCode(0);

        // Should create API controller
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('JsonResponse', $controllerContent);
        $this->assertStringContainsString('auth:sanctum', $controllerContent);

        // Should NOT create Vue views
        $viewsPath = resource_path("js/pages/{$this->testFeaturePlural}");
        $this->assertFalse($this->files->exists($viewsPath));

        // Should create API routes
        $routePath = base_path("routes/Modules/{$this->testFeaturePlural}/api.php");
        $this->assertTrue($this->files->exists($routePath));
    }

    /** @test */
    public function it_can_select_view_only_mode_interactively()
    {
        $command = $this->artisan('features:create', ['name' => $this->testModelName])
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '3', // View Only
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            );

        $command->assertExitCode(0);

        // Should create View controller
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('Inertia', $controllerContent);
        $this->assertStringNotContainsString('JsonResponse', $controllerContent);

        // Should create Vue views
        $viewsPath = resource_path("js/pages/{$this->testFeaturePlural}");
        $this->assertTrue($this->files->exists($viewsPath));

        // Should NOT create requests
        $storeRequestPath = app_path("Http/Requests/Store{$this->testModelName}Request.php");
        $this->assertFalse($this->files->exists($storeRequestPath));

        // Should create web routes
        $routePath = base_path("routes/Modules/{$this->testFeaturePlural}/web.php");
        $this->assertTrue($this->files->exists($routePath));
    }

    /** @test */
    public function it_skips_interactive_menu_when_api_option_provided()
    {
        // When --api is provided, should not show interactive menu
        $result = Artisan::call('features:create', [
            'name' => $this->testModelName,
            '--api' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create API controller directly without prompting
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('JsonResponse', $controllerContent);
    }

    /** @test */
    public function it_skips_interactive_menu_when_view_option_provided()
    {
        // When --view is provided, should not show interactive menu
        $result = Artisan::call('features:create', [
            'name' => $this->testModelName,
            '--view' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create View controller directly without prompting
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('Inertia', $controllerContent);
    }

    /** @test */
    public function it_shows_mode_selection_confirmation_messages()
    {
        $command = $this->artisan('features:create', ['name' => $this->testModelName])
            ->expectsChoice(
                '🤔 Pilih mode generation',
                '2',
                [
                    '1' => 'Full-stack (API + Views)',
                    '2' => 'API Only',
                    '3' => 'View Only'
                ]
            )
            ->expectsOutput('   ✅ Mode API Only dipilih');

        $command->assertExitCode(0);
    }
}
