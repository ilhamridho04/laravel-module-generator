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

    protected function makeModel(string $name, bool $force = false): void
    {
        $modelPath = app_path("Models/{$name}.php");

        if ($force || !$this->files->exists($modelPath)) {
            $stub = $this->renderStub('model.stub', [
                'model' => $name,
                'table' => Str::snake(Str::pluralStudly($name)),
            ]);
            $this->files->put($modelPath, $stub);
            $this->appendSoftDeleteToModel($modelPath, $name);
            $this->line("📋 Model dibuat: {$name}.php");
        } else {
            $this->warn("📋 Model sudah ada: {$name}.php");
        }
    }

    protected function appendSoftDeleteToModel(string $path, string $name): void
    {
        $content = file_get_contents($path);
        $content = str_replace(
            'use Illuminate\\Database\\Eloquent\\Model;',
            "use Illuminate\\Database\\Eloquent\\Model;\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;",
            $content
        );

        $content = str_replace(
            "class {$name} extends Model",
            "class {$name} extends Model\n{\n    use SoftDeletes;",
            $content
        );

        file_put_contents($path, $content);
    }

    protected function makeMigration(string $name, bool $force = false): void
    {
        $table = Str::snake(Str::pluralStudly($name));
        $this->callSilent('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
        $this->line("🗄️ Migration dibuat: create_{$table}_table");
    }

    protected function makeController(string $name, string $plural, bool $force = false): void
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");

        if ($force || !$this->files->exists($controllerPath)) {
            $stub = $this->renderStub('controller.stub', [
                'model' => $name,
                'plural' => $plural,
                'table' => Str::snake($plural),
                'kebab' => Str::kebab($plural),
            ]);
            $this->files->put($controllerPath, $stub);
            $this->line("🎮 Controller dibuat: {$name}Controller.php");
        } else {
            $this->warn("🎮 Controller sudah ada: {$name}Controller.php");
        }
    }

    protected function makeRequests(string $name, bool $force = false): void
    {
        $storePath = app_path("Http/Requests/Store{$name}Request.php");
        $updatePath = app_path("Http/Requests/Update{$name}Request.php");

        $storeExists = $this->files->exists($storePath);
        $updateExists = $this->files->exists($updatePath);

        if ($force || !$storeExists) {
            $store = $this->renderStub('request.store.stub', ['model' => $name]);
            $this->files->put($storePath, $store);
            $this->line("📝 Request dibuat: Store{$name}Request.php");
        } else {
            $this->warn("📝 Request sudah ada: Store{$name}Request.php");
        }

        if ($force || !$updateExists) {
            $update = $this->renderStub('request.update.stub', ['model' => $name]);
            $this->files->put($updatePath, $update);
            $this->line("📝 Request dibuat: Update{$name}Request.php");
        } else {
            $this->warn("📝 Request sudah ada: Update{$name}Request.php");
        }
    }

    protected function makeViews(string $name, string $plural, bool $force = false): void
    {
        $table = Str::snake($plural);
        $viewPath = resource_path("js/pages/{$plural}");
        $views = ['Index', 'Create', 'Edit', 'Show'];

        foreach ($views as $view) {
            $filePath = "{$viewPath}/{$view}.vue";
            if ($force || !$this->files->exists($filePath)) {
                $stub = $this->renderStub("views/{$view}.vue.stub", [
                    'model' => $name,
                    'plural' => $plural,
                    'table' => $table,
                    'name' => Str::singular($plural),
                ]);
                $this->files->put($filePath, $stub);
                $this->line("🖼️ View dibuat: {$view}.vue");
            } else {
                $this->warn("🖼️ View sudah ada: {$view}.vue");
            }
        }
    }

    protected function injectRoutes(string $name, string $plural, string $kebab, bool $force = false): void
    {
        $variable = Str::camel($name);
        $controller = "App\\Http\\Controllers\\{$name}Controller";
        $content = $this->renderStub('routes.stub', [
            'plural' => $plural,
            'kebab' => $kebab,
            'variable' => $variable,
            'controller' => $controller,
        ]);

        $routePath = base_path("routes/Modules/{$plural}/web.php");
        if ($force || !$this->files->exists($routePath)) {
            $this->files->put($routePath, $content);
            $this->line("🛣️ Route file dibuat: routes/Modules/{$plural}/web.php");
        } else {
            $this->warn("🛣️ Routes sudah ada: routes/Modules/{$plural}/web.php");
        }
    }

    protected function makePermissionSeeder(string $plural, bool $force = false): void
    {
        $class = "{$plural}PermissionSeeder";
        $path = database_path("seeders/Permission/{$class}.php");
        $permissions = $this->renderStub('seeder.permission.stub', [
            'class' => $class,
            'permission' => Str::snake($plural, ' '),
        ]);

        if ($force || !$this->files->exists($path)) {
            $this->files->put($path, $permissions);
            $this->line("🔐 Permission seeder {$class} dibuat.");
        } else {
            $this->warn("🔐 Permission seeder {$class} sudah ada.");
        }
    }

    // Pastikan method lain (makeModel, makeMigration, makeController, makeRequests, makeViews, injectRoutes, makePermissionSeeder) juga menerima $force jika perlu.
}

// End of file: src/Commands/MakeFeature.php