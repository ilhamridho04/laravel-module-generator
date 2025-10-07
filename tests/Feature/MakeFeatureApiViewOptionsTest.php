<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureApiViewOptionsTest extends TestCase
{
    protected Filesystem $files;
    protected string $testModelName = 'TestProduct';
    protected string $testFeaturePlural = 'TestProducts';

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem();
        $this->cleanupTestFiles();
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
            app_path("Http/Controllers/Api/{$this->testModelName}Controller.php"), // API controller
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

    /** @test */
    public function it_prevents_using_both_api_and_view_options()
    {
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
            '--api' => true,
            '--view' => true,
        ]);

        $this->assertEquals(0, $result); // Command should run but show error
        $output = Artisan::output();
        $this->assertStringContainsString('Tidak bisa menggunakan --api dan --view bersamaan', $output);
    }    /** @test */
    public function it_can_generate_api_only_feature()
    {
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
            '--api' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create API controller in API folder
        $controllerPath = app_path("Http/Controllers/Api/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('namespace App\Http\Controllers\Api', $controllerContent);
        $this->assertStringContainsString('JsonResponse', $controllerContent);
        $this->assertStringContainsString('auth:sanctum', $controllerContent);
        $this->assertStringNotContainsString('Inertia', $controllerContent);

        // Should create requests
        $storeRequestPath = app_path("Http/Requests/Store{$this->testModelName}Request.php");
        $updateRequestPath = app_path("Http/Requests/Update{$this->testModelName}Request.php");
        $this->assertTrue($this->files->exists($storeRequestPath));
        $this->assertTrue($this->files->exists($updateRequestPath));

        // Should create API routes
        $routePath = base_path("routes/Modules/{$this->testFeaturePlural}/api.php");
        $this->assertTrue($this->files->exists($routePath));

        $routeContent = $this->files->get($routePath);
        $this->assertStringContainsString('auth:sanctum', $routeContent);
        $this->assertStringContainsString('apiResource', $routeContent);
        $this->assertStringContainsString('App\Http\Controllers\Api', $routeContent);

        // Should NOT create Vue views
        $viewsPath = resource_path("js/pages/{$this->testFeaturePlural}");
        $this->assertFalse($this->files->exists($viewsPath));

        // Should create model and other common files
        $modelPath = app_path("Models/{$this->testModelName}.php");
        $this->assertTrue($this->files->exists($modelPath));
    }

    /** @test */
    public function it_can_generate_view_only_feature()
    {
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
            '--view' => true,
        ]);

        $this->assertEquals(0, $result);

        // Should create View controller
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('Inertia', $controllerContent);
        $this->assertStringContainsString('auth', $controllerContent);
        $this->assertStringNotContainsString('JsonResponse', $controllerContent);
        $this->assertStringNotContainsString('auth:sanctum', $controllerContent);

        // Should NOT create requests (view-only mode uses simple validation)
        $storeRequestPath = app_path("Http/Requests/Store{$this->testModelName}Request.php");
        $updateRequestPath = app_path("Http/Requests/Update{$this->testModelName}Request.php");
        $this->assertFalse($this->files->exists($storeRequestPath));
        $this->assertFalse($this->files->exists($updateRequestPath));

        // Should create web routes
        $routePath = base_path("routes/Modules/{$this->testFeaturePlural}/web.php");
        $this->assertTrue($this->files->exists($routePath));

        $routeContent = $this->files->get($routePath);
        $this->assertStringContainsString('auth', $routeContent);
        $this->assertStringContainsString('verified', $routeContent);
        $this->assertStringNotContainsString('apiResource', $routeContent);

        // Should create Vue views
        $viewsPath = resource_path("js/pages/{$this->testFeaturePlural}");
        $this->assertTrue($this->files->exists($viewsPath));

        $views = ['Index.vue', 'Create.vue', 'Edit.vue', 'Show.vue'];
        foreach ($views as $view) {
            $viewPath = "{$viewsPath}/{$view}";
            $this->assertTrue($this->files->exists($viewPath));
        }

        // Should create model and other common files
        $modelPath = app_path("Models/{$this->testModelName}.php");
        $this->assertTrue($this->files->exists($modelPath));
    }

    /** @test */
    public function it_can_generate_full_stack_feature_by_default()
    {
        $result = Artisan::call('module:create', [
            'name' => $this->testModelName,
        ]);

        $this->assertEquals(0, $result);

        // Should create full-stack controller
        $controllerPath = app_path("Http/Controllers/{$this->testModelName}Controller.php");
        $this->assertTrue($this->files->exists($controllerPath));

        $controllerContent = $this->files->get($controllerPath);
        $this->assertStringContainsString('Inertia', $controllerContent);

        // Should create requests
        $storeRequestPath = app_path("Http/Requests/Store{$this->testModelName}Request.php");
        $updateRequestPath = app_path("Http/Requests/Update{$this->testModelName}Request.php");
        $this->assertTrue($this->files->exists($storeRequestPath));
        $this->assertTrue($this->files->exists($updateRequestPath));

        // Should create web routes
        $routePath = base_path("routes/Modules/{$this->testFeaturePlural}/web.php");
        $this->assertTrue($this->files->exists($routePath));

        // Should create Vue views
        $viewsPath = resource_path("js/pages/{$this->testFeaturePlural}");
        $this->assertTrue($this->files->exists($viewsPath));

        // Should create model and other common files
        $modelPath = app_path("Models/{$this->testModelName}.php");
        $this->assertTrue($this->files->exists($modelPath));
    }

    /** @test */
    public function it_shows_correct_generation_mode_in_output()
    {
        // Test API mode
        Artisan::call('module:create', ['name' => 'TestApiMode', '--api' => true]);
        $apiOutput = Artisan::output();
        $this->assertStringContainsString('Mode: API Only', $apiOutput);

        // Test View mode
        Artisan::call('module:create', ['name' => 'TestViewMode', '--view' => true]);
        $viewOutput = Artisan::output();
        $this->assertStringContainsString('Mode: View Only', $viewOutput);

        // Test Full-stack mode
        Artisan::call('module:create', ['name' => 'TestFullMode']);
        $fullOutput = Artisan::output();
        $this->assertStringContainsString('Mode: Full-stack (API + View)', $fullOutput);

        // Cleanup
        $testModels = ['TestApiMode', 'TestViewMode', 'TestFullMode'];
        foreach ($testModels as $model) {
            $paths = [
                app_path("Http/Controllers/{$model}Controller.php"),
                app_path("Http/Requests/Store{$model}Request.php"),
                app_path("Http/Requests/Update{$model}Request.php"),
                app_path("Models/{$model}.php"),
                resource_path("js/pages/" . Str::pluralStudly($model)),
                base_path("routes/Modules/" . Str::pluralStudly($model)),
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
    }
}
