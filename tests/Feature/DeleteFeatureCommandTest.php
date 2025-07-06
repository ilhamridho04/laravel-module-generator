<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DeleteFeatureCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any existing test files
        $this->cleanupTestFiles();
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        $this->cleanupTestFiles();

        parent::tearDown();
    }

    protected function cleanupTestFiles(): void
    {
        $filesToClean = [
            app_path('Models/TestUser.php'),
            app_path('Http/Controllers/TestUserController.php'),
            app_path('Http/Requests/StoreTestUserRequest.php'),
            app_path('Http/Requests/UpdateTestUserRequest.php'),
            resource_path('js/pages/TestUsers'),
            base_path('routes/Modules/TestUsers'),
            database_path('seeders/Permission/TestUsersPermissionSeeder.php'),
            app_path('Enums/TestUserStatus.php'),
            app_path('Observers/TestUserObserver.php'),
        ];

        foreach ($filesToClean as $file) {
            if (File::exists($file)) {
                if (is_dir($file)) {
                    File::deleteDirectory($file);
                } else {
                    File::delete($file);
                }
            }
        }
    }

    /** @test */
    public function it_can_delete_basic_feature_files()
    {
        // First create a feature
        Artisan::call('make:feature', ['name' => 'TestUser']);

        // Verify files exist
        $this->assertFileExists(app_path('Models/TestUser.php'));
        $this->assertFileExists(app_path('Http/Controllers/TestUserController.php'));

        // Delete the feature
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--force' => true
        ]);

        // Verify files are deleted
        $this->assertFileDoesNotExist(app_path('Models/TestUser.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/TestUserController.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/StoreTestUserRequest.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/UpdateTestUserRequest.php'));
    }

    /** @test */
    public function it_can_delete_vue_components()
    {
        // Create feature
        Artisan::call('make:feature', ['name' => 'TestUser']);

        // Verify Vue files exist
        $this->assertFileExists(resource_path('js/pages/TestUsers/Index.vue'));
        $this->assertFileExists(resource_path('js/pages/TestUsers/Create.vue'));
        $this->assertFileExists(resource_path('js/pages/TestUsers/Edit.vue'));
        $this->assertFileExists(resource_path('js/pages/TestUsers/Show.vue'));

        // Delete feature
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--force' => true
        ]);

        // Verify Vue files are deleted
        $this->assertFileDoesNotExist(resource_path('js/pages/TestUsers/Index.vue'));
        $this->assertFileDoesNotExist(resource_path('js/pages/TestUsers/Create.vue'));
        $this->assertFileDoesNotExist(resource_path('js/pages/TestUsers/Edit.vue'));
        $this->assertFileDoesNotExist(resource_path('js/pages/TestUsers/Show.vue'));

        // Verify directory is cleaned up
        $this->assertDirectoryDoesNotExist(resource_path('js/pages/TestUsers'));
    }

    /** @test */
    public function it_can_delete_optional_components()
    {
        // Create feature with optional components
        Artisan::call('make:feature', [
            'name' => 'TestUser',
            '--with' => ['enum', 'observer']
        ]);

        // Verify optional files exist
        $this->assertFileExists(app_path('Enums/TestUserStatus.php'));
        $this->assertFileExists(app_path('Observers/TestUserObserver.php'));

        // Delete feature with optional components
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--with' => ['enum', 'observer'],
            '--force' => true
        ]);

        // Verify optional files are deleted
        $this->assertFileDoesNotExist(app_path('Enums/TestUserStatus.php'));
        $this->assertFileDoesNotExist(app_path('Observers/TestUserObserver.php'));
    }

    /** @test */
    public function it_can_delete_all_components_with_all_flag()
    {
        // Create feature with all optional components
        Artisan::call('make:feature', [
            'name' => 'TestUser',
            '--with' => ['enum', 'observer']
        ]);

        // Delete with --all flag
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--all' => true,
            '--force' => true
        ]);

        // Verify all files are deleted
        $this->assertFileDoesNotExist(app_path('Models/TestUser.php'));
        $this->assertFileDoesNotExist(app_path('Enums/TestUserStatus.php'));
        $this->assertFileDoesNotExist(app_path('Observers/TestUserObserver.php'));
    }

    /** @test */
    public function it_cleans_up_empty_directories()
    {
        // Create feature
        Artisan::call('make:feature', ['name' => 'TestUser']);

        // Verify directories exist
        $this->assertDirectoryExists(resource_path('js/pages/TestUsers'));
        $this->assertDirectoryExists(base_path('routes/Modules/TestUsers'));

        // Delete feature
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--force' => true
        ]);

        // Verify empty directories are cleaned up
        $this->assertDirectoryDoesNotExist(resource_path('js/pages/TestUsers'));
        $this->assertDirectoryDoesNotExist(base_path('routes/Modules/TestUsers'));
    }

    /** @test */
    public function it_handles_non_existent_feature_gracefully()
    {
        // Try to delete a feature that doesn't exist
        $exitCode = Artisan::call('delete:feature', [
            'name' => 'NonExistentFeature',
            '--force' => true
        ]);

        // Should not throw an error
        $this->assertEquals(0, $exitCode);

        $output = Artisan::output();
        $this->assertStringContainsString('Tidak ada file yang ditemukan', $output);
    }

    /** @test */
    public function it_shows_files_before_deletion()
    {
        // Create feature
        Artisan::call('make:feature', ['name' => 'TestUser']);

        // Delete without force to see the file list
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--force' => true // Still use force to avoid interactive prompt in test
        ]);

        $output = Artisan::output();
        $this->assertStringContainsString('File yang akan dihapus:', $output);
        $this->assertStringContainsString('TestUser.php', $output);
    }

    /** @test */
    public function it_finds_and_deletes_migration_files()
    {
        // Create feature (this creates migration)
        Artisan::call('make:feature', ['name' => 'TestUser']);

        // Find migration files
        $migrationFiles = File::glob(database_path('migrations/*_create_test_users_table.php'));

        // Should have created a migration
        $this->assertNotEmpty($migrationFiles);

        // Delete feature
        Artisan::call('delete:feature', [
            'name' => 'TestUser',
            '--force' => true
        ]);

        // Migration should be deleted
        $migrationFiles = File::glob(database_path('migrations/*_create_test_users_table.php'));
        $this->assertEmpty($migrationFiles);
    }
}
