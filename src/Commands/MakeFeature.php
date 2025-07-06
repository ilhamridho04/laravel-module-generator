<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeFeature extends Command
{
    protected $signature = 'make:feature {name}';
    protected $description = 'Generate full CRUD feature with module structure';

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

        $this->info("\n🔧 Membuat fitur: $plural ($kebab)");

        $this->createFolders($plural);
        $this->makeModel($name);
        $this->makeMigration($name);
        $this->makeController($name, $plural);
        $this->makeRequests($name);
        $this->makeViews($name, $plural);
        $this->injectRoutes($name, $plural, $kebab);
        $this->makePermissionSeeder($plural);

        $this->info("\n✅ Fitur $plural berhasil dibuat!");
    }

    protected function createFolders(string $plural): void
    {
        $paths = [
            app_path("Models"),
            app_path("Http/Requests"),
            resource_path("js/pages/{$plural}"),
            database_path("seeders/Permission"),
            base_path("routes/Modules/{$plural}"),
        ];

        foreach ($paths as $path) {
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
                $this->line("📁 Folder dibuat: $path");
            }
        }
    }

    protected function makeModel(string $name): void
    {
        $this->callSilent('make:model', ['name' => $name]);
        $path = app_path("Models/{$name}.php");
        $this->appendSoftDeleteToModel($path, $name);
        $this->line("📄 Model $name dibuat.");
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

    protected function makeMigration(string $name): void
    {
        $this->callSilent('make:migration', [
            'name' => 'create_' . Str::snake(Str::plural($name)) . '_table',
        ]);
    }

    protected function makeController(string $name, string $plural): void
    {
        $table = Str::snake($plural);
        $content = $this->renderStub('controller.stub', [
            'model' => $name,
            'plural' => $plural,
            'table' => $table,
        ]);

        $this->files->put(app_path("Http/Controllers/{$name}Controller.php"), $content);
        $this->line("📄 Controller {$name}Controller dibuat.");
    }

    protected function makeRequests(string $name): void
    {
        $store = $this->renderStub('request.store.stub', ['model' => $name]);
        $update = $this->renderStub('request.update.stub', ['model' => $name]);

        $this->files->put(app_path("Http/Requests/Store{$name}Request.php"), $store);
        $this->files->put(app_path("Http/Requests/Update{$name}Request.php"), $update);
        $this->line("📄 Store & Update Request dibuat.");
    }

    protected function makeViews(string $name, string $plural): void
    {
        $table = Str::snake($plural);
        $viewPath = resource_path("js/pages/{$plural}");
        $views = ['Index', 'Create', 'Edit', 'Show'];

        foreach ($views as $view) {
            $stub = $this->renderStub("views/{$view}.vue.stub", [
                'model' => $name,
                'plural' => $plural,
                'table' => $table,
                'name' => Str::singular($plural)
            ]);
            $this->files->put("{$viewPath}/{$view}.vue", $stub);
        }

        $this->line("📄 Vue Pages Index/Create/Edit/Show dibuat.");
    }

    protected function injectRoutes(string $name, string $plural, string $kebab): void
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
        $this->files->put($routePath, $content);
        $this->line("🛣️  Route file dibuat.");
    }

    protected function makePermissionSeeder(string $plural): void
    {
        $class = "{$plural}PermissionSeeder";
        $path = database_path("seeders/Permission/{$class}.php");
        $permissions = $this->renderStub('seeder.permission.stub', [
            'class' => $class,
            'permission' => Str::snake($plural, ' '),
        ]);

        $this->files->put($path, $permissions);
        $this->line("🔐 Permission seeder {$class} dibuat.");
    }

    protected function renderStub(string $stubPath, array $replacements): string
    {
        $stub = file_get_contents(__DIR__ . '/../stubs/' . $stubPath);
        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }
        return $stub;
    }
}
