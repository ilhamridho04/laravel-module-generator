<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFeature extends Command
{
    protected $signature = 'make:feature {name} {--with=* : Optional components like enum, observer, policy, factory, test} {--force : Overwrite existing files}';
    protected $description = 'Generate full CRUD feature with module structure and optional components';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem;
    }

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $plural = Str::pluralStudly($name);
        $kebab = Str::kebab($plural);
        $force = $this->option('force');

        $this->info("\n🔧 Membuat fitur: $plural ($kebab)");

        $this->createFolders($plural);
        $this->makeModel($name, $force);
        $this->makeMigration($name, $force);
        $this->makeController($name, $plural, $force);
        $this->makeRequests($name, $force);
        $this->makeViews($name, $plural, $force);
        $this->injectRoutes($name, $plural, $kebab, $force);
        $this->makePermissionSeeder($plural, $force);

        // Optional components
        $options = collect($this->option('with'));
        if ($options->contains('test')) {
            $this->makeTest($name, $plural, $force);
        }
        if ($options->contains('factory')) {
            $this->callSilent('make:factory', [
                'name' => "{$name}Factory",
                '--model' => $name,
                '--force' => $force,
            ]);
            $this->line("🏭 Factory dibuat.");
        }
        if ($options->contains('policy')) {
            $this->callSilent('make:policy', [
                'name' => "{$name}Policy",
                '--model' => $name,
                '--force' => $force,
            ]);
            $this->line("🛡️  Policy dibuat.");
        }
        if ($options->contains('enum')) {
            $enumPath = app_path("Enums/$name" . "Status.php");
            if (!$this->files->exists(dirname($enumPath))) {
                $this->files->makeDirectory(dirname($enumPath), 0755, true);
            }
            if ($force || !$this->files->exists($enumPath)) {
                $enum = $this->renderStub('Enum.stub', ['model' => $name]);
                $this->files->put($enumPath, $enum);
                $this->line("📚 Enum dibuat: $enumPath");
            } else {
                $this->warn("📚 Enum sudah ada: $enumPath");
            }
        }
        if ($options->contains('observer')) {
            $observerPath = app_path("Observers/{$name}Observer.php");
            if (!$this->files->exists(dirname($observerPath))) {
                $this->files->makeDirectory(dirname($observerPath), 0755, true);
            }
            if ($force || !$this->files->exists($observerPath)) {
                $observer = $this->renderStub('Observer.stub', ['model' => $name]);
                $this->files->put($observerPath, $observer);
                $this->line("👁️ Observer dibuat: $observerPath");
                $this->registerObserverInServiceProvider($name);
            } else {
                $this->warn("👁️ Observer sudah ada: $observerPath");
            }
        }

        $this->info("\n✅ Fitur $plural berhasil dibuat!");
    }

    protected function createFolders(string $plural): void
    {
        $paths = [
            app_path("Models"),
            app_path("Http/Requests"),
            resource_path("js/pages/$plural"),
            database_path("seeders/Permission"),
            base_path("routes/Modules/$plural"),
            app_path("Enums"),
            app_path("Observers"),
        ];

        foreach ($paths as $path) {
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
                $this->line("📁 Folder dibuat: $path");
            }
        }
    }

    protected function makeTest(string $name, string $plural, bool $force = false): void
    {
        $table = Str::snake($plural);
        $stub = $this->renderStub('tests/FeatureTest.stub', [
            'name' => $name,
            'plural' => $plural,
            'table' => $table,
            'model' => $name,
        ]);

        $testPath = base_path("tests/Feature/{$name}FeatureTest.php");
        if ($force || !$this->files->exists($testPath)) {
            $this->files->put($testPath, $stub);
            $this->line("🧪 Feature test dibuat: {$name}FeatureTest.php");
        } else {
            $this->warn("🧪 Feature test sudah ada: {$name}FeatureTest.php");
        }
    }

    protected function renderStub(string $stubPath, array $replacements): string
    {
        $custom = base_path("stubs/laravel-module-generator/{$stubPath}");
        $default = __DIR__ . '/../stubs/' . $stubPath;

        $stub = file_exists($custom) ? file_get_contents($custom) : file_get_contents($default);

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }
        return $stub;
    }

    protected function registerObserverInServiceProvider(string $name): void
    {
        $providerPath = app_path('Providers/AppServiceProvider.php');
        if (!$this->files->exists($providerPath)) {
            $this->warn("⚠️ AppServiceProvider.php tidak ditemukan.");
            return;
        }

        $content = $this->files->get($providerPath);
        $modelUse = "use App\\Models\\{$name};";
        $observerUse = "use App\\Observers\\{$name}Observer;";
        $observeLine = "        {$name}::observe({$name}Observer::class);";

        // Tambah use jika belum ada
        if (!str_contains($content, $modelUse)) {
            $content = preg_replace(
                '/namespace App\\Providers;\n*/',
                "namespace App\\Providers;\n\n{$modelUse}\n{$observerUse}\n",
                $content
            );
        }

        // Tambah observer ke method boot()
        if (!str_contains($content, $observeLine)) {
            $content = preg_replace_callback(
                '/public function boot\(\)(\s*): void\s*\{/',
                fn($matches) => $matches[0] . "\n{$observeLine}",
                $content
            );
        }

        $this->files->put($providerPath, $content);
        $this->line("✅ Observer {$name}Observer di-register otomatis di AppServiceProvider.");
    }

    // Pastikan method lain (makeModel, makeMigration, makeController, makeRequests, makeViews, injectRoutes, makePermissionSeeder) juga menerima $force jika perlu.
}

// End of file: src/Commands/MakeFeature.php