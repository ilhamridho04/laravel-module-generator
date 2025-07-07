<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeFeatureCommandIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any existing test files
        $this->cleanupTestFiles();
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        $this->cleanupTestFiles();
        parent::tearDown();
    }

    /** @test */
    public function it_can_run_make_feature_command_successfully()
    {
        // Test that the command can be called without errors
        $exitCode = Artisan::call('modules:create', [
            'name' => 'TestPost',
            '--force' => true
        ]);

        $this->assertEquals(0, $exitCode, 'Command should exit successfully');

        $output = Artisan::output();
        $this->assertStringContainsString('TestPosts', $output);
    }

    /** @test */
    public function it_creates_model_file()
    {
        Artisan::call('modules:create', [
            'name' => 'Article',
            '--force' => true
        ]);

        $modelPath = app_path('Models/Article.php');
        $this->assertFileExists($modelPath);

        $content = File::get($modelPath);
        $this->assertStringContainsString('class Article extends Model', $content);
        $this->assertStringContainsString('use HasFactory, SoftDeletes;', $content);
    }

    /** @test */
    public function it_creates_controller_file()
    {
        Artisan::call('modules:create', [
            'name' => 'Category',
            '--force' => true
        ]);

        $controllerPath = app_path('Http/Controllers/CategoryController.php');
        $this->assertFileExists($controllerPath);

        $content = File::get($controllerPath);
        $this->assertStringContainsString('class CategoryController extends Controller', $content);
        $this->assertStringContainsString('public function index(Request $request)', $content);
        $this->assertStringContainsString('public function store(', $content);
        $this->assertStringContainsString('public function update(', $content);
        $this->assertStringContainsString('public function destroy(', $content);
    }

    /** @test */
    public function it_creates_request_files()
    {
        Artisan::call('modules:create', [
            'name' => 'Tag',
            '--force' => true
        ]);

        $storeRequestPath = app_path('Http/Requests/StoreTagRequest.php');
        $updateRequestPath = app_path('Http/Requests/UpdateTagRequest.php');

        $this->assertFileExists($storeRequestPath);
        $this->assertFileExists($updateRequestPath);

        $storeContent = File::get($storeRequestPath);
        $this->assertStringContainsString('class StoreTagRequest extends FormRequest', $storeContent);

        $updateContent = File::get($updateRequestPath);
        $this->assertStringContainsString('class UpdateTagRequest extends FormRequest', $updateContent);
    }

    /** @test */
    public function it_creates_migration_file()
    {
        Artisan::call('modules:create', [
            'name' => 'Comment',
            '--force' => true
        ]);

        // Find migration file (it has timestamp in name)
        $migrationFiles = File::glob(database_path('migrations/*_create_comments_table.php'));
        $this->assertNotEmpty($migrationFiles, 'Migration file should be created');

        $content = File::get($migrationFiles[0]);
        $this->assertStringContainsString('Schema::create(\'comments\'', $content);
        $this->assertStringContainsString('Schema::dropIfExists(\'comments\')', $content);
    }

    /** @test */
    public function it_creates_vue_view_files()
    {
        Artisan::call('modules:create', [
            'name' => 'Post',
            '--force' => true
        ]);

        $viewsPath = resource_path('js/pages/Posts');

        $this->assertFileExists($viewsPath . '/Index.vue');
        $this->assertFileExists($viewsPath . '/Create.vue');
        $this->assertFileExists($viewsPath . '/Edit.vue');
        $this->assertFileExists($viewsPath . '/Show.vue');

        $indexContent = File::get($viewsPath . '/Index.vue');
        $this->assertStringContainsString('<template>', $indexContent);
        // Check for either JavaScript or TypeScript script setup
        $this->assertTrue(
            str_contains($indexContent, '<script setup>') || str_contains($indexContent, '<script setup lang="ts">'),
            "Vue component should contain either '<script setup>' or '<script setup lang=\"ts\">'"
        );
    }

    /** @test */
    public function it_creates_route_file()
    {
        Artisan::call('modules:create', [
            'name' => 'User',
            '--force' => true
        ]);

        $routePath = base_path('routes/Modules/Users/web.php');
        $this->assertFileExists($routePath);

        $content = File::get($routePath);
        $this->assertStringContainsString('Route::', $content);
        $this->assertStringContainsString('UserController', $content);
    }

    /** @test */
    public function it_creates_permission_seeder()
    {
        Artisan::call('modules:create', [
            'name' => 'Product',
            '--force' => true
        ]);

        $seederPath = database_path('seeders/Permission/ProductsPermissionSeeder.php');
        $this->assertFileExists($seederPath);

        $content = File::get($seederPath);
        $this->assertStringContainsString('class ProductsPermissionSeeder extends Seeder', $content);
        $this->assertStringContainsString('Permission::firstOrCreate', $content);
    }

    /** @test */
    public function it_handles_force_option_correctly()
    {
        // Create first time
        Artisan::call('modules:create', ['name' => 'TestItem']);

        $modelPath = app_path('Models/TestItem.php');
        $this->assertFileExists($modelPath);

        // Modify the file
        File::put($modelPath, '<?php // This is modified content');

        // Run again without force - should not overwrite
        $exitCode = Artisan::call('modules:create', ['name' => 'TestItem']);
        $this->assertEquals(0, $exitCode);

        $content = File::get($modelPath);
        $this->assertStringContainsString('// This is modified content', $content);

        // Run again with force - should overwrite
        Artisan::call('modules:create', ['name' => 'TestItem', '--force' => true]);

        $content = File::get($modelPath);
        $this->assertStringNotContainsString('// This is modified content', $content);
        $this->assertStringContainsString('class TestItem extends Model', $content);
    }

    /** @test */
    public function it_can_create_optional_components()
    {
        Artisan::call('modules:create', [
            'name' => 'Order',
            '--with' => ['enum', 'observer'],
            '--force' => true
        ]);

        // Check basic files are created
        $this->assertFileExists(app_path('Models/Order.php'));

        // Check optional components
        $this->assertFileExists(app_path('Enums/OrderStatus.php'));
        $this->assertFileExists(app_path('Observers/OrderObserver.php'));

        $enumContent = File::get(app_path('Enums/OrderStatus.php'));
        $this->assertStringContainsString('enum OrderStatus', $enumContent);

        $observerContent = File::get(app_path('Observers/OrderObserver.php'));
        $this->assertStringContainsString('class OrderObserver', $observerContent);
    }

    protected function cleanupTestFiles(): void
    {
        $pathsToClean = [
            app_path('Models'),
            app_path('Http/Controllers'),
            app_path('Http/Requests'),
            app_path('Enums'),
            app_path('Observers'),
            resource_path('js/pages'),
            base_path('routes/Modules'),
            database_path('seeders/Permission'),
        ];

        foreach ($pathsToClean as $path) {
            if (File::exists($path) && File::isDirectory($path)) {
                // Only clean our test files, not the entire directory
                $testModels = ['TestPost', 'Article', 'Category', 'Tag', 'Comment', 'Post', 'User', 'Product', 'TestItem', 'Order'];

                foreach ($testModels as $model) {
                    // Clean model files
                    $modelFile = app_path("Models/{$model}.php");
                    if (File::exists($modelFile)) {
                        File::delete($modelFile);
                    }

                    // Clean controller files
                    $controllerFile = app_path("Http/Controllers/{$model}Controller.php");
                    if (File::exists($controllerFile)) {
                        File::delete($controllerFile);
                    }

                    // Clean request files
                    $storeRequestFile = app_path("Http/Requests/Store{$model}Request.php");
                    $updateRequestFile = app_path("Http/Requests/Update{$model}Request.php");
                    if (File::exists($storeRequestFile))
                        File::delete($storeRequestFile);
                    if (File::exists($updateRequestFile))
                        File::delete($updateRequestFile);

                    // Clean enum files
                    $enumFile = app_path("Enums/{$model}Status.php");
                    if (File::exists($enumFile)) {
                        File::delete($enumFile);
                    }

                    // Clean observer files
                    $observerFile = app_path("Observers/{$model}Observer.php");
                    if (File::exists($observerFile)) {
                        File::delete($observerFile);
                    }

                    // Clean view directories
                    $viewDir = resource_path("js/pages/{$model}s");
                    if (File::exists($viewDir)) {
                        File::deleteDirectory($viewDir);
                    }

                    // Clean route directories
                    $routeDir = base_path("routes/Modules/{$model}s");
                    if (File::exists($routeDir)) {
                        File::deleteDirectory($routeDir);
                    }

                    // Clean permission seeders
                    $seederFile = database_path("seeders/Permission/{$model}sPermissionSeeder.php");
                    if (File::exists($seederFile)) {
                        File::delete($seederFile);
                    }
                }
            }
        }

        // Clean migration files
        $migrationPattern = database_path('migrations/*_create_*_table.php');
        $migrationFiles = File::glob($migrationPattern);
        foreach ($migrationFiles as $file) {
            $testTables = ['test_posts', 'articles', 'categories', 'tags', 'comments', 'posts', 'users', 'products', 'test_items', 'orders'];
            foreach ($testTables as $table) {
                if (str_contains($file, $table)) {
                    File::delete($file);
                    break;
                }
            }
        }
    }
}
