<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureFullStackTest extends TestCase
{
    protected Filesystem $files;

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

    private function cleanupTestFiles(): void
    {
        $paths = [
            app_path('Models/Product.php'),
            app_path('Http/Controllers/ProductController.php'),
            app_path('Http/Controllers/API/ProductController.php'),
            app_path('Http/Requests/StoreProductRequest.php'),
            app_path('Http/Requests/UpdateProductRequest.php'),
            app_path('Traits/ApiResponser.php'),
            base_path('routes/Modules/Products'),
            database_path('migrations'),
            database_path('seeders/Permission/ProductPermissionSeeder.php'),
            resource_path('js/Pages/Products'),
            app_path('Models/Order.php'),
            app_path('Http/Controllers/OrderController.php'),
            app_path('Http/Controllers/API/OrderController.php'),
            base_path('routes/Modules/Orders'),
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

        // Clean up migration files
        $migrationFiles = $this->files->glob(database_path('migrations/*_create_products_table.php'));
        foreach ($migrationFiles as $file) {
            $this->files->delete($file);
        }

        $migrationFiles = $this->files->glob(database_path('migrations/*_create_orders_table.php'));
        foreach ($migrationFiles as $file) {
            $this->files->delete($file);
        }
    }

    /** @test */
    public function it_generates_both_api_and_web_components_in_full_stack_mode()
    {
        // Create dummy routes to avoid auto-install prompt
        $this->createDummyRoutes();

        // Test interactive mode selection for full-stack
        $this->artisan('modules:create', ['name' => 'Product', '--skip-install' => true])
            ->expectsChoice('🤔 Pilih mode generation', '1', [
                '1' => 'Full-stack (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->expectsOutput('   ✅ Mode Full-stack dipilih')
            ->expectsOutput('🔧 Membuat fitur: Products (products) - Mode: Full-stack (API + View)')
            ->expectsOutput('🎮 API Controller dibuat: API/ProductController.php')
            ->expectsOutput('🎮 Web Controller dibuat: ProductController.php')
            ->expectsOutput('🛣️ API route file dibuat: routes/Modules/Products/api.php')
            ->expectsOutput('🛣️ Web route file dibuat: routes/Modules/Products/web.php')
            ->assertExitCode(0);

        // Verify both controllers were created
        $this->assertFileExists(app_path('Http/Controllers/ProductController.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/ProductController.php'));

        // Verify API controller uses correct namespace and trait
        $apiControllerContent = $this->files->get(app_path('Http/Controllers/API/ProductController.php'));
        $this->assertStringContainsString('namespace App\Http\Controllers\API;', $apiControllerContent);
        $this->assertStringContainsString('use App\Traits\ApiResponser;', $apiControllerContent);
        $this->assertStringContainsString('use ApiResponser;', $apiControllerContent);

        // Verify web controller uses standard namespace
        $webControllerContent = $this->files->get(app_path('Http/Controllers/ProductController.php'));
        $this->assertStringContainsString('namespace App\Http\Controllers;', $webControllerContent);

        // Verify both route files were created
        $this->assertFileExists(base_path('routes/Modules/Products/api.php'));
        $this->assertFileExists(base_path('routes/Modules/Products/web.php'));

        // Verify API routes point to API controller
        $apiRoutesContent = $this->files->get(base_path('routes/Modules/Products/api.php'));
        $this->assertStringContainsString('App\Http\Controllers\API\ProductController', $apiRoutesContent);

        // Verify web routes point to web controller
        $webRoutesContent = $this->files->get(base_path('routes/Modules/Products/web.php'));
        $this->assertStringContainsString('App\Http\Controllers\ProductController', $webRoutesContent);

        // Verify requests were created (for API)
        $this->assertFileExists(app_path('Http/Requests/StoreProductRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/UpdateProductRequest.php'));

        // Verify views were created (for web)
        $this->assertFileExists(resource_path('js/Pages/Products/Index.vue'));
        $this->assertFileExists(resource_path('js/Pages/Products/Create.vue'));
        $this->assertFileExists(resource_path('js/Pages/Products/Edit.vue'));
        $this->assertFileExists(resource_path('js/Pages/Products/Show.vue'));

        // Verify other common files
        $this->assertFileExists(app_path('Models/Product.php'));
        $this->assertFileExists(app_path('Traits/ApiResponser.php'));
    }

    /** @test */
    public function it_generates_both_api_and_web_components_without_interactive_mode()
    {
        // Create dummy routes to avoid auto-install prompt
        $this->createDummyRoutes();

        // Test without any flags (should default to full-stack)
        $this->artisan('modules:create', ['name' => 'Order', '--skip-install' => true])
            ->expectsChoice('🤔 Pilih mode generation', '1', [
                '1' => 'Full-stack (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->assertExitCode(0);

        // Verify both controllers were created
        $this->assertFileExists(app_path('Http/Controllers/OrderController.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/OrderController.php'));

        // Verify both route files were created
        $this->assertFileExists(base_path('routes/Modules/Orders/api.php'));
        $this->assertFileExists(base_path('routes/Modules/Orders/web.php'));
    }

    private function createDummyRoutes(): void
    {
        // Create basic route files to avoid auto-install prompts
        $routesPath = base_path('routes');
        if (!$this->files->exists($routesPath)) {
            $this->files->makeDirectory($routesPath, 0755, true);
        }

        $this->files->put(base_path('routes/web.php'), "<?php\n\n// Dummy routes file for testing\n");
        $this->files->put(base_path('routes/api.php'), "<?php\n\n// Dummy API routes file for testing\n");

        // Create modules loader files
        $this->files->put(base_path('routes/modules.php'), "<?php\n\n// Dummy modules loader for testing\n");
        $this->files->put(base_path('routes/api-modules.php'), "<?php\n\n// Dummy API modules loader for testing\n");
    }
}
