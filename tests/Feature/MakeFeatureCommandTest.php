<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeFeatureCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up generated files after each test
        $this->cleanupGeneratedFiles();
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_basic_crud_feature()
    {
        // Run the command
        $exitCode = Artisan::call('features:create', ['name' => 'TestPost']);

        $this->assertEquals(0, $exitCode);

        // Check if basic files are created
        $this->assertFileExists(app_path('Models/TestPost.php'));
        $this->assertFileExists(app_path('Http/Controllers/TestPostController.php'));
        $this->assertFileExists(app_path('Http/Requests/StoreTestPostRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/UpdateTestPostRequest.php'));

        // Check if migration file exists
        $migrationFiles = File::glob(database_path('migrations/*_create_test_posts_table.php'));
        $this->assertNotEmpty($migrationFiles, 'Migration file should be created');

        // Check if route file exists
        $this->assertFileExists(base_path('routes/Modules/TestPosts/web.php'));

        // Check if Vue views are created
        $this->assertFileExists(resource_path('js/pages/TestPosts/Index.vue'));
        $this->assertFileExists(resource_path('js/pages/TestPosts/Create.vue'));
        $this->assertFileExists(resource_path('js/pages/TestPosts/Edit.vue'));
        $this->assertFileExists(resource_path('js/pages/TestPosts/Show.vue'));

        // Check if permission seeder is created
        $this->assertFileExists(database_path('seeders/Permission/TestPostsPermissionSeeder.php'));
    }

    /** @test */
    public function it_can_generate_feature_with_optional_components()
    {
        // Run with optional components
        $exitCode = Artisan::call('features:create', [
            'name' => 'Product',
            '--with' => ['enum', 'observer', 'factory', 'policy']
        ]);

        $this->assertEquals(0, $exitCode);

        // Check basic files
        $this->assertFileExists(app_path('Models/Product.php'));

        // Check optional components
        $this->assertFileExists(app_path('Enums/ProductStatus.php'));
        $this->assertFileExists(app_path('Observers/ProductObserver.php'));

        // Factory and Policy would be created by Laravel's built-in commands
        // We just check that the command completed successfully
    }

    /** @test */
    public function it_generates_correct_model_content()
    {
        Artisan::call('features:create', ['name' => 'Article']);

        $modelContent = File::get(app_path('Models/Article.php'));

        // Check namespace and class
        $this->assertStringContainsString('namespace App\Models;', $modelContent);
        $this->assertStringContainsString('class Article extends Model', $modelContent);
        $this->assertStringContainsString('use HasFactory, SoftDeletes;', $modelContent);

        // Check basic structure
        $this->assertStringContainsString('protected $table = \'articles\';', $modelContent);
        $this->assertStringContainsString('protected $fillable', $modelContent);
    }

    /** @test */
    public function it_generates_correct_controller_content()
    {
        Artisan::call('features:create', ['name' => 'Category']);

        $controllerContent = File::get(app_path('Http/Controllers/CategoryController.php'));

        // Check namespace and class
        $this->assertStringContainsString('namespace App\Http\Controllers;', $controllerContent);
        $this->assertStringContainsString('class CategoryController extends Controller', $controllerContent);

        // Check methods
        $this->assertStringContainsString('public function index(Request $request)', $controllerContent);
        $this->assertStringContainsString('public function create()', $controllerContent);
        $this->assertStringContainsString('public function store(', $controllerContent);
        $this->assertStringContainsString('public function show(', $controllerContent);
        $this->assertStringContainsString('public function edit(', $controllerContent);
        $this->assertStringContainsString('public function update(', $controllerContent);
        $this->assertStringContainsString('public function destroy(', $controllerContent);
    }

    /** @test */
    public function it_generates_correct_request_files()
    {
        Artisan::call('features:create', ['name' => 'Tag']);

        $storeRequestContent = File::get(app_path('Http/Requests/StoreTagRequest.php'));
        $updateRequestContent = File::get(app_path('Http/Requests/UpdateTagRequest.php'));

        // Check Store Request
        $this->assertStringContainsString('class StoreTagRequest extends FormRequest', $storeRequestContent);
        $this->assertStringContainsString('public function authorize()', $storeRequestContent);
        $this->assertStringContainsString('public function rules()', $storeRequestContent);

        // Check Update Request
        $this->assertStringContainsString('class UpdateTagRequest extends FormRequest', $updateRequestContent);
        $this->assertStringContainsString('public function authorize()', $updateRequestContent);
        $this->assertStringContainsString('public function rules()', $updateRequestContent);
    }

    /** @test */
    public function it_can_handle_force_option()
    {
        // Create the feature first
        Artisan::call('features:create', ['name' => 'Comment']);

        // Modify one of the files
        $modelPath = app_path('Models/Comment.php');
        File::put($modelPath, '<?php // Modified content');

        // Run again with force
        $exitCode = Artisan::call('features:create', [
            'name' => 'Comment',
            '--force' => true
        ]);

        $this->assertEquals(0, $exitCode);

        // Check that file was overwritten
        $content = File::get($modelPath);
        $this->assertStringNotContainsString('// Modified content', $content);
        $this->assertStringContainsString('class Comment extends Model', $content);
    }

    /** @test */
    public function it_generates_vue_components_with_correct_structure()
    {
        Artisan::call('features:create', ['name' => 'Post']);

        $indexVue = File::get(resource_path('js/pages/Posts/Index.vue'));
        $createVue = File::get(resource_path('js/pages/Posts/Create.vue'));

        // Check Vue structure
        $this->assertStringContainsString('<template>', $indexVue);
        // Check for either JavaScript or TypeScript script setup
        $this->assertTrue(
            str_contains($indexVue, '<script setup>') || str_contains($indexVue, '<script setup lang="ts">'),
            "Index Vue component should contain either '<script setup>' or '<script setup lang=\"ts\">'"
        );
        $this->assertStringContainsString('<template>', $createVue);
        $this->assertTrue(
            str_contains($createVue, '<script setup>') || str_contains($createVue, '<script setup lang="ts">'),
            "Create Vue component should contain either '<script setup>' or '<script setup lang=\"ts\">'"
        );
    }

    protected function cleanupGeneratedFiles(): void
    {
        $directories = [
            app_path('Models'),
            app_path('Http/Controllers'),
            app_path('Http/Requests'),
            app_path('Enums'),
            app_path('Observers'),
            resource_path('js/pages'),
            base_path('routes/Modules'),
            database_path('seeders/Permission'),
        ];

        foreach ($directories as $directory) {
            if (File::exists($directory)) {
                File::cleanDirectory($directory);
            }
        }

        // Clean migration files
        $migrationFiles = File::glob(database_path('migrations/????_??_??_??????_create_*_table.php'));
        foreach ($migrationFiles as $file) {
            if (
                str_contains($file, 'create_test_posts_table') ||
                str_contains($file, 'create_products_table') ||
                str_contains($file, 'create_articles_table') ||
                str_contains($file, 'create_categories_table') ||
                str_contains($file, 'create_tags_table') ||
                str_contains($file, 'create_comments_table') ||
                str_contains($file, 'create_posts_table')
            ) {
                File::delete($file);
            }
        }
    }
}
