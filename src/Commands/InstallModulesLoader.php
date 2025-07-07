<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallModulesLoader extends Command
{
    protected $signature = 'modules:install {--force : Force reinstall even if already installed}';
    protected $description = 'Install and integrate modules auto-loader (web and API) into Laravel routes';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem;
    }

    public function handle(): void
    {
        $force = $this->option('force');

        $this->info("\n🚀 Installing Laravel Module Generator auto-loader...");

        // Step 1: Create loaders if they don't exist
        $this->ensureModulesLoader($force);
        $this->ensureApiModulesLoader($force);

        // Step 2: Integrate into routes
        $this->integrateIntoRoutes($force);
        $this->integrateIntoApiRoutes($force);

        $this->info("\n✅ Modules loader berhasil diinstall dan diintegrasikan!");
        $this->info("🎯 Sekarang Anda dapat membuat module dengan: php artisan modules:create NamaFeature");
    }

    protected function ensureModulesLoader(bool $force = false): void
    {
        $loaderPath = base_path('routes/modules.php');

        if (!$force && $this->files->exists($loaderPath)) {
            $this->line("✅ routes/modules.php sudah ada");
            return;
        }

        // Create modules loader
        $stub = $this->renderStub('modules-loader.stub', []);
        $this->files->put($loaderPath, $stub);
        $this->line("✅ Web auto-loader dibuat: routes/modules.php");
    }

    protected function ensureApiModulesLoader(bool $force = false): void
    {
        $loaderPath = base_path('routes/api-modules.php');

        if (!$force && $this->files->exists($loaderPath)) {
            $this->line("✅ routes/api-modules.php sudah ada");
            return;
        }

        // Create API modules loader
        $stub = $this->renderStub('api-modules-loader.stub', []);
        $this->files->put($loaderPath, $stub);
        $this->line("✅ API auto-loader dibuat: routes/api-modules.php");
    }

    protected function integrateIntoRoutes(bool $force = false): void
    {
        $appRoutesPath = base_path('routes/app.php'); // Laravel 11+
        $webRoutesPath = base_path('routes/web.php'); // Laravel 10 and below
        $loaderInclude = "require __DIR__ . '/modules.php';";

        // Try app.php first (Laravel 11+), then web.php
        $targetPath = null;
        $targetName = null;

        if ($this->files->exists($appRoutesPath)) {
            $targetPath = $appRoutesPath;
            $targetName = 'routes/app.php';
        } elseif ($this->files->exists($webRoutesPath)) {
            $targetPath = $webRoutesPath;
            $targetName = 'routes/web.php';
        } else {
            $this->error("❌ Tidak dapat menemukan routes/app.php atau routes/web.php");
            return;
        }

        $content = $this->files->get($targetPath);

        // Check if already included
        if (str_contains($content, "require __DIR__ . '/modules.php'")) {
            $this->line("✅ Auto-loader sudah terintegrasi di {$targetName}");
            return;
        }

        if (!$force && str_contains($content, 'modules.php')) {
            $this->warn("⚠️  Kemungkinan modules.php sudah diinclude dengan cara lain di {$targetName}");
            if (!$this->confirm("Tetap tambahkan require statement?")) {
                return;
            }
        }

        // Add the require statement at the end of the file
        $content = rtrim($content);
        $content .= "\n\n// Auto-load module routes\n";
        $content .= "{$loaderInclude}\n";

        $this->files->put($targetPath, $content);
        $this->line("✅ Web auto-loader diintegrasikan ke {$targetName}");
    }

    protected function integrateIntoApiRoutes(bool $force = false): void
    {
        $apiRoutesPath = base_path('routes/api.php');
        $loaderInclude = "require __DIR__ . '/api-modules.php';";

        if (!$this->files->exists($apiRoutesPath)) {
            $this->warn("⚠️  routes/api.php tidak ditemukan. API auto-loader tidak diintegrasikan.");
            return;
        }

        $content = $this->files->get($apiRoutesPath);

        // Check if already included
        if (str_contains($content, "require __DIR__ . '/api-modules.php'")) {
            $this->line("✅ API auto-loader sudah terintegrasi di routes/api.php");
            return;
        }

        if (!$force && str_contains($content, 'api-modules.php')) {
            $this->warn("⚠️  Kemungkinan api-modules.php sudah diinclude dengan cara lain di routes/api.php");
            if (!$this->confirm("Tetap tambahkan require statement?")) {
                return;
            }
        }

        // Add the require statement at the end of the file
        $content = rtrim($content);
        $content .= "\n\n// Auto-load API module routes\n";
        $content .= "{$loaderInclude}\n";

        $this->files->put($apiRoutesPath, $content);
        $this->line("✅ API auto-loader diintegrasikan ke routes/api.php");
    }

    protected function renderStub(string $stubPath, array $replacements): string
    {
        $custom = base_path("stubs/laravel-module-generator/{$stubPath}");
        $defaultStub = __DIR__ . '/../stubs/' . $stubPath;

        // Check if custom stub exists first, then default
        if (file_exists($custom)) {
            $stub = file_get_contents($custom);
        } elseif (file_exists($defaultStub)) {
            $stub = file_get_contents($defaultStub);
        } else {
            $this->error("Stub file tidak ditemukan: {$stubPath}");
            return '';
        }

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }

        return $stub;
    }
}
