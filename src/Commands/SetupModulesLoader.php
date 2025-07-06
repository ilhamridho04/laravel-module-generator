<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SetupModulesLoader extends Command
{
    protected $signature = 'modules:setup {--force : Overwrite existing files}';
    protected $description = 'Setup automatic module routes loader for Laravel Module Generator';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem;
    }

    public function handle(): void
    {
        $force = $this->option('force');

        $this->info("\n🔧 Setting up Laravel Module Generator auto-loader...");

        $this->createModulesLoader($force);
        $this->suggestIntegration();

        $this->info("\n✅ Modules loader setup completed!");
    }

    protected function createModulesLoader(bool $force = false): void
    {
        $loaderPath = base_path('routes/modules.php');

        if (!$force && $this->files->exists($loaderPath)) {
            $this->warn("⚠️  routes/modules.php sudah ada. Gunakan --force untuk menimpa.");
            return;
        }

        // Create modules loader
        $stub = $this->renderStub('modules-loader.stub', []);
        $this->files->put($loaderPath, $stub);
        $this->line("✅ Auto-loader dibuat: routes/modules.php");
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

    protected function suggestIntegration(): void
    {
        $this->info("\n📋 Langkah selanjutnya:");
        $this->line("1. Tambahkan baris berikut ke routes/web.php atau routes/app.php (Laravel 11+):");
        $this->line("   <fg=yellow>require __DIR__ . '/modules.php';</>");
        $this->line("");
        $this->line("2. Atau gunakan command berikut untuk otomatis menambahkannya:");
        $this->line("   <fg=cyan>php artisan modules:install</>");
        $this->line("");

        $webPath = base_path('routes/web.php');
        $appPath = base_path('routes/app.php');

        if ($this->files->exists($appPath)) {
            $this->line("📁 Terdeteksi Laravel 11+ - Anda dapat menambahkan ke routes/app.php");
        } elseif ($this->files->exists($webPath)) {
            $this->line("📁 Terdeteksi Laravel klasik - Tambahkan ke routes/web.php");
        }
    }
}
