<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class DeleteFeatureInteractiveTest extends TestCase
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
            app_path('Models/TestProduct.php'),
            app_path('Http/Controllers/TestProductController.php'),
            app_path('Http/Controllers/API/TestProductController.php'),
            app_path('Http/Requests/StoreTestProductRequest.php'),
            app_path('Http/Requests/UpdateTestProductRequest.php'),
            app_path('Traits/ApiResponser.php'),
            base_path('routes/Modules/TestProducts'),
            database_path('seeders/Permission/TestProductsPermissionSeeder.php'),
            resource_path('js/pages/TestProducts'),
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
        $migrationFiles = $this->files->glob(database_path('migrations/*_create_test_products_table.php'));
        foreach ($migrationFiles as $file) {
            $this->files->delete($file);
        }
    }

    private function createTestFiles(): void
    {
        // Create dummy routes to avoid auto-install prompt
        $this->createDummyRoutes();

        // Create all components for testing deletion using interactive mode
        $this->artisan('modules:create', ['name' => 'TestProduct', '--skip-install' => true])
            ->expectsChoice('🤔 Pilih mode generation', '1', [
                '1' => 'Full-stack (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ]);
    }

    private function createDummyRoutes(): void
    {
        $routesPath = base_path('routes');
        if (!$this->files->exists($routesPath)) {
            $this->files->makeDirectory($routesPath, 0755, true);
        }

        $this->files->put(base_path('routes/web.php'), "<?php\n\n// Dummy routes file for testing\n");
        $this->files->put(base_path('routes/api.php'), "<?php\n\n// Dummy API routes file for testing\n");
        $this->files->put(base_path('routes/modules.php'), "<?php\n\n// Dummy modules loader for testing\n");
        $this->files->put(base_path('routes/api-modules.php'), "<?php\n\n// Dummy API modules loader for testing\n");
    }

    /** @test */
    public function it_shows_interactive_deletion_menu_when_no_mode_specified()
    {
        $this->createTestFiles();

        // Verify files exist before deletion
        $this->assertFileExists(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/TestProductController.php'));

        $this->artisan('modules:delete', ['name' => 'TestProduct', '--force' => true])
            ->expectsChoice('🤔 Pilih mode deletion', '1', [
                '1' => 'Full Deletion (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->expectsOutput('   ✅ Mode Full Deletion dipilih')
            ->expectsOutput('🗑️ Menghapus fitur: TestProducts (test-products) - Mode: Full Deletion (API + View)')
            ->assertExitCode(0);

        // Verify all files are deleted
        $this->assertFileDoesNotExist(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/API/TestProductController.php'));
        $this->assertFileDoesNotExist(app_path('Models/TestProduct.php'));
        $this->assertFileDoesNotExist(base_path('routes/Modules/TestProducts/web.php'));
        $this->assertFileDoesNotExist(base_path('routes/Modules/TestProducts/api.php'));
    }

    /** @test */
    public function it_can_delete_api_only_components_interactively()
    {
        $this->createTestFiles();

        // Verify files exist before deletion
        $this->assertFileExists(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/TestProductController.php'));
        $this->assertFileExists(resource_path('js/pages/TestProducts/Index.vue'));

        $this->artisan('modules:delete', ['name' => 'TestProduct', '--force' => true])
            ->expectsChoice('🤔 Pilih mode deletion', '2', [
                '1' => 'Full Deletion (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->expectsOutput('   ✅ Mode API Only dipilih')
            ->expectsOutput('🗑️ Menghapus fitur: TestProducts (test-products) - Mode: API Only')
            ->assertExitCode(0);

        // Verify only API components are deleted
        $this->assertFileDoesNotExist(app_path('Http/Controllers/API/TestProductController.php'));
        $this->assertFileDoesNotExist(base_path('routes/Modules/TestProducts/api.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/StoreTestProductRequest.php'));

        // Verify web components still exist
        $this->assertFileExists(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileExists(resource_path('js/pages/TestProducts/Index.vue'));
        $this->assertFileExists(base_path('routes/Modules/TestProducts/web.php'));
    }

    /** @test */
    public function it_can_delete_view_only_components_interactively()
    {
        $this->createTestFiles();

        // Verify files exist before deletion
        $this->assertFileExists(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/TestProductController.php'));
        $this->assertFileExists(resource_path('js/pages/TestProducts/Index.vue'));

        $this->artisan('modules:delete', ['name' => 'TestProduct', '--force' => true])
            ->expectsChoice('🤔 Pilih mode deletion', '3', [
                '1' => 'Full Deletion (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ])
            ->expectsOutput('   ✅ Mode View Only dipilih')
            ->expectsOutput('🗑️ Menghapus fitur: TestProducts (test-products) - Mode: View Only')
            ->assertExitCode(0);

        // Verify only view components are deleted
        $this->assertFileDoesNotExist(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileDoesNotExist(resource_path('js/pages/TestProducts/Index.vue'));
        $this->assertFileDoesNotExist(base_path('routes/Modules/TestProducts/web.php'));

        // Verify API components still exist
        $this->assertFileExists(app_path('Http/Controllers/API/TestProductController.php'));
        $this->assertFileExists(base_path('routes/Modules/TestProducts/api.php'));
        $this->assertFileExists(app_path('Http/Requests/StoreTestProductRequest.php'));
    }

    /** @test */
    public function it_skips_interactive_menu_when_api_option_provided()
    {
        $this->createTestFiles();

        $this->artisan('modules:delete', ['name' => 'TestProduct', '--api' => true, '--force' => true])
            ->doesntExpectOutput('🎯 Pilih mode penghapusan fitur:')
            ->expectsOutput('🗑️ Menghapus fitur: TestProducts (test-products) - Mode: API Only')
            ->assertExitCode(0);

        // Verify only API components are deleted
        $this->assertFileDoesNotExist(app_path('Http/Controllers/API/TestProductController.php'));
        $this->assertFileExists(app_path('Http/Controllers/TestProductController.php'));
    }

    /** @test */
    public function it_skips_interactive_menu_when_view_option_provided()
    {
        $this->createTestFiles();

        $this->artisan('modules:delete', ['name' => 'TestProduct', '--view' => true, '--force' => true])
            ->doesntExpectOutput('🎯 Pilih mode penghapusan fitur:')
            ->expectsOutput('🗑️ Menghapus fitur: TestProducts (test-products) - Mode: View Only')
            ->assertExitCode(0);

        // Verify only view components are deleted
        $this->assertFileDoesNotExist(app_path('Http/Controllers/TestProductController.php'));
        $this->assertFileExists(app_path('Http/Controllers/API/TestProductController.php'));
    }

    /** @test */
    public function it_prevents_using_both_api_and_view_options()
    {
        $this->artisan('modules:delete', ['name' => 'TestProduct', '--api' => true, '--view' => true])
            ->expectsOutput('❌ Tidak bisa menggunakan --api dan --view bersamaan. Pilih salah satu atau kosongkan untuk full deletion.')
            ->assertExitCode(0);
    }
}
