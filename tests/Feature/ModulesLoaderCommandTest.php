<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Feature;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModulesLoaderCommandTest extends TestCase
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
            base_path('routes/modules.php'),
            base_path('routes/test-web.php'),
        ];

        foreach ($filesToClean as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }

    /** @test */
    public function it_can_setup_modules_loader()
    {
        // Run setup command
        Artisan::call('modules:setup', ['--force' => true]);

        // Check if modules.php is created
        $this->assertFileExists(base_path('routes/modules.php'));

        $content = File::get(base_path('routes/modules.php'));
        $this->assertStringContainsString('Auto-load all module routes', $content);
        $this->assertStringContainsString('routes/Modules', $content);
        $this->assertStringContainsString('File::isDirectory', $content);
    }

    /** @test */
    public function it_wont_overwrite_existing_modules_loader_without_force()
    {
        // Create a modules.php file
        File::put(base_path('routes/modules.php'), '<?php // existing content');

        // Run setup without force
        Artisan::call('modules:setup');

        $content = File::get(base_path('routes/modules.php'));
        $this->assertStringContainsString('existing content', $content);
        $this->assertStringNotContainsString('Auto-load all module routes', $content);
    }

    /** @test */
    public function it_can_overwrite_with_force_flag()
    {
        // Create a modules.php file
        File::put(base_path('routes/modules.php'), '<?php // existing content');

        // Run setup with force
        Artisan::call('modules:setup', ['--force' => true]);

        $content = File::get(base_path('routes/modules.php'));
        $this->assertStringNotContainsString('existing content', $content);
        $this->assertStringContainsString('Auto-load all module routes', $content);
    }

    /** @test */
    public function it_can_install_modules_loader()
    {
        // Create a temporary web.php file for testing
        $webPath = base_path('routes/test-web.php');
        File::put($webPath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\nRoute::get('/', function () {\n    return view('welcome');\n});");

        // Mock the integration method by testing the content
        $this->assertTrue(File::exists($webPath));

        // Run install command 
        Artisan::call('modules:setup', ['--force' => true]);

        // Verify modules.php is created
        $this->assertFileExists(base_path('routes/modules.php'));
    }

    /** @test */
    public function modules_loader_content_is_correct()
    {
        Artisan::call('modules:setup', ['--force' => true]);

        $content = File::get(base_path('routes/modules.php'));

        // Check for essential parts
        $this->assertStringContainsString('use Illuminate\Support\Facades\File;', $content);
        $this->assertStringContainsString('use Illuminate\Support\Facades\Route;', $content);
        $this->assertStringContainsString("base_path('routes/Modules')", $content);
        $this->assertStringContainsString('File::isDirectory($modulesPath)', $content);
        $this->assertStringContainsString('File::directories($modulesPath)', $content);
        $this->assertStringContainsString("require \$routeFile;", $content);
    }

    /** @test */
    public function it_provides_helpful_output_messages()
    {
        Artisan::call('modules:setup', ['--force' => true]);

        $output = Artisan::output();
        $this->assertStringContainsString('Setting up Laravel Module Generator', $output);
        $this->assertStringContainsString('Auto-loader dibuat:', $output);
        $this->assertStringContainsString('routes/modules.php', $output);
        $this->assertStringContainsString('Langkah selanjutnya:', $output);
    }
}
