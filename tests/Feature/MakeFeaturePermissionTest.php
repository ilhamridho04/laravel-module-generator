<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Concerns\WithWorkbench;

class MakeFeaturePermissionTest extends TestCase
{
    use WithWorkbench;

    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $this->cleanupTestFiles();
        parent::tearDown();
    }

    /** @test */
    public function it_adds_permission_middleware_to_api_controller()
    {
        $this->artisan('module:create Product --api --force')
            ->assertExitCode(0);

        $controllerPath = app_path('Http/Controllers/Api/ProductController.php');
        $this->assertFileExists($controllerPath);

        $content = $this->files->get($controllerPath);

        // Check if permission middleware is added
        $this->assertStringContainsString("permission:view products", $content);
        $this->assertStringContainsString("permission:create products", $content);
        $this->assertStringContainsString("permission:update products", $content);
        $this->assertStringContainsString("permission:delete products", $content);

        // Check middleware is applied to correct methods
        $this->assertStringContainsString("->only(['index', 'show'])", $content);
        $this->assertStringContainsString("->only(['store'])", $content);
        $this->assertStringContainsString("->only(['update'])", $content);
        $this->assertStringContainsString("->only(['destroy'])", $content);
    }

    /** @test */
    public function it_adds_permission_middleware_to_web_controller()
    {
        $this->artisan('module:create Category --view --force')
            ->assertExitCode(0);

        $controllerPath = app_path('Http/Controllers/CategoryController.php');
        $this->assertFileExists($controllerPath);

        $content = $this->files->get($controllerPath);

        // Check if permission middleware is added
        $this->assertStringContainsString("permission:view categories", $content);
        $this->assertStringContainsString("permission:create categories", $content);
        $this->assertStringContainsString("permission:update categories", $content);
        $this->assertStringContainsString("permission:delete categories", $content);
    }

    /** @test */
    public function it_adds_permission_middleware_to_fullstack_controller()
    {
        $this->artisan('module:create Order --force')
            ->expectsChoice('🤔 Pilih mode generation', '1', [
                '1' => 'Full-stack (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->assertExitCode(0);

        $controllerPath = app_path('Http/Controllers/OrderController.php');
        $this->assertFileExists($controllerPath);

        $content = $this->files->get($controllerPath);

        // Check if permission middleware is added
        $this->assertStringContainsString("permission:view orders", $content);
        $this->assertStringContainsString("permission:create orders", $content);
        $this->assertStringContainsString("permission:update orders", $content);
        $this->assertStringContainsString("permission:delete orders", $content);
    }

    /** @test */
    public function it_generates_permission_seeder_with_correct_permissions()
    {
        $this->artisan('module:create User --force')
            ->expectsChoice('🤔 Pilih mode generation', '1', [
                '1' => 'Full-stack (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->assertExitCode(0);

        $seederPath = database_path('seeders/Permission/UsersPermissionSeeder.php');
        $this->assertFileExists($seederPath);

        $content = $this->files->get($seederPath);

        // Check if correct permissions are generated
        $this->assertStringContainsString("'view', 'create', 'update', 'delete', 'restore', 'force_delete'", $content);
        $this->assertStringContainsString('$perm users', $content);
        $this->assertStringContainsString('UsersPermissionSeeder', $content);
    }

    private function cleanupTestFiles(): void
    {
        $filesToClean = [
            app_path('Http/Controllers/Api/ProductController.php'),
            app_path('Http/Controllers/CategoryController.php'),
            app_path('Http/Controllers/OrderController.php'),
            app_path('Http/Controllers/UserController.php'),
            app_path('Models/Product.php'),
            app_path('Models/Category.php'),
            app_path('Models/Order.php'),
            app_path('Models/User.php'),
            database_path('seeders/Permission/ProductsPermissionSeeder.php'),
            database_path('seeders/Permission/CategoriesPermissionSeeder.php'),
            database_path('seeders/Permission/OrdersPermissionSeeder.php'),
            database_path('seeders/Permission/UsersPermissionSeeder.php'),
        ];

        foreach ($filesToClean as $file) {
            if ($this->files->exists($file)) {
                $this->files->delete($file);
            }
        }

        // Clean up directories if empty
        $dirsToClean = [
            app_path('Http/Controllers/Api'),
            database_path('seeders/Permission'),
        ];

        foreach ($dirsToClean as $dir) {
            if ($this->files->exists($dir) && count($this->files->allFiles($dir)) === 0) {
                $this->files->deleteDirectory($dir);
            }
        }
    }
}
