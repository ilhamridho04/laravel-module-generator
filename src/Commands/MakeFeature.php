<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFeature extends Command
{
    protected $signature = 'features:create {name} 
                            {--with=* : Optional components like enum, observer, policy, factory, test} 
                            {--force : Overwrite existing files}
                            {--api : Generate API-only (without Vue views)}
                            {--view : Generate View-only (without API routes)}
                            {--skip-install : Skip auto-install prompt for routes}';
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
        $apiOnly = $this->option('api');
        $viewOnly = $this->option('view');

        // Validate options
        if ($apiOnly && $viewOnly) {
            $this->error('❌ Tidak bisa menggunakan --api dan --view bersamaan. Pilih salah satu atau kosongkan untuk full-stack.');
            return;
        }

        // If no mode is specified via options, show interactive menu
        if (!$apiOnly && !$viewOnly) {
            $generationMode = $this->showGenerationModeMenu();

            switch ($generationMode) {
                case 'api':
                    $apiOnly = true;
                    break;
                case 'view':
                    $viewOnly = true;
                    break;
                case 'full':
                default:
                    // Full-stack mode (default behavior) - keep both flags as false
                    break;
            }
        }

        // Determine generation mode
        $mode = $this->determineGenerationMode($apiOnly, $viewOnly);
        $this->info("\n🔧 Membuat fitur: $plural ($kebab) - Mode: {$mode}");

        $this->createFolders($plural, $apiOnly);

        // Ensure ApiResponser trait exists if generating API controllers
        if ($apiOnly || (!$apiOnly && !$viewOnly)) {
            $this->ensureApiResponserTrait();
        }

        $this->ensureModulesLoader($force);
        $this->makeModel($name, $force);
        $this->makeMigration($name, $force);
        $this->makeController($name, $plural, $force, $apiOnly, $viewOnly);

        // Generate requests only if not view-only mode
        if (!$viewOnly) {
            $this->makeRequests($name, $force);
        }

        // Generate views only if not API-only mode
        if (!$apiOnly) {
            $this->makeViews($name, $plural, $force);
        }

        $this->injectRoutes($name, $plural, $kebab, $force, $apiOnly, $viewOnly);
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
            ]);
            $this->line("🏭 Factory dibuat.");
        }
        if ($options->contains('policy')) {
            $this->callSilent('make:policy', [
                'name' => "{$name}Policy",
                '--model' => $name,
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

    protected function determineGenerationMode(bool $apiOnly, bool $viewOnly): string
    {
        if ($apiOnly) {
            return 'API Only';
        }

        if ($viewOnly) {
            return 'View Only';
        }

        return 'Full-stack (API + View)';
    }

    protected function showGenerationModeMenu(): string
    {
        $this->info("\n🎯 Pilih mode pembuatan fitur:");
        $this->line("   <fg=cyan>1.</> Full-stack (API + Views) - Lengkap dengan controller, routes, views");
        $this->line("   <fg=yellow>2.</> API Only - Hanya API controller, routes, dan requests");
        $this->line("   <fg=green>3.</> View Only - Hanya Vue views dan web controller");
        $this->line("");

        $choice = $this->choice(
            '🤔 Pilih mode generation',
            [
                '1' => 'Full-stack (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ],
            '1' // Default to full-stack
        );

        // Map choice to mode
        switch ($choice) {
            case '2':
                $this->line("   <fg=yellow>✅ Mode API Only dipilih</>");
                return 'api';
            case '3':
                $this->line("   <fg=green>✅ Mode View Only dipilih</>");
                return 'view';
            case '1':
            default:
                $this->line("   <fg=cyan>✅ Mode Full-stack dipilih</>");
                return 'full';
        }
    }

    protected function createFolders(string $plural, bool $skipViews = false): void
    {
        $paths = [
            app_path("Models"),
            app_path("Http/Controllers"),
            app_path("Http/Controllers/API"), // For API controllers
            app_path("Http/Requests"),
            app_path("Traits"), // For shared traits like ApiResponser
            database_path("seeders/Permission"),
            base_path("routes/Modules/$plural"),
            app_path("Enums"),
            app_path("Observers"),
        ];

        // Only create views folder if not API-only mode
        if (!$skipViews) {
            $paths[] = resource_path("js/pages/$plural");
        }

        foreach ($paths as $path) {
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
                $this->line("📁 Folder dibuat: $path");
            }
        }
    }

    protected function ensureApiResponserTrait(): void
    {
        $traitPath = app_path('Traits/ApiResponser.php');

        if (!$this->files->exists($traitPath)) {
            // Create Traits directory if it doesn't exist
            $traitsDir = app_path('Traits');
            if (!$this->files->exists($traitsDir)) {
                $this->files->makeDirectory($traitsDir, 0755, true);
                $this->line("📁 Folder dibuat: $traitsDir");
            }

            // Create the ApiResponser trait
            $stub = $this->files->get(__DIR__ . '/../stubs/api-responser.trait.stub');
            $this->files->put($traitPath, $stub);
            $this->line("🔧 ApiResponser trait dibuat: Traits/ApiResponser.php");
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
        $defaultStub = __DIR__ . '/../stubs/' . $stubPath;
        $defaultView = __DIR__ . '/../views/' . $stubPath;

        // Check if custom stub exists first, then default stubs, then views
        if (file_exists($custom)) {
            $stub = file_get_contents($custom);
        } elseif (file_exists($defaultStub)) {
            $stub = file_get_contents($defaultStub);
        } elseif (file_exists($defaultView)) {
            $stub = file_get_contents($defaultView);
        } else {
            // Create a basic stub if none exists
            $this->warn("⚠️ Stub tidak ditemukan: {$stubPath}, menggunakan template dasar");
            $stub = $this->getBasicStub($stubPath);
        }

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }
        return $stub;
    }

    protected function getBasicStub(string $stubPath): string
    {
        // Return basic templates for missing stubs
        switch ($stubPath) {
            case 'controller.stub':
                return "<?php\n\nnamespace App\\Http\\Controllers;\n\nuse Illuminate\\Http\\Request;\n\nclass {{ model }}Controller extends Controller\n{\n    // Add your methods here\n}\n";
            case 'controller.api.stub':
                return "<?php\n\nnamespace App\\Http\\Controllers\\API;\n\nuse Illuminate\\Http\\Request;\nuse App\\Models\\{{ model }};\nuse App\\Http\\Controllers\\Controller;\n\nclass {{ model }}Controller extends Controller\n{\n    public function index() { return {{ model }}::paginate(10); }\n    public function store(Request \$request) { return {{ model }}::create(\$request->all()); }\n    public function show({{ model }} \${{ table }}) { return \${{ table }}; }\n    public function update(Request \$request, {{ model }} \${{ table }}) { \${{ table }}->update(\$request->all()); return \${{ table }}; }\n    public function destroy({{ model }} \${{ table }}) { \${{ table }}->delete(); return response()->noContent(); }\n}\n";
            case 'controller.view.stub':
                return "<?php\n\nnamespace App\\Http\\Controllers;\n\nuse Illuminate\\Http\\Request;\nuse Inertia\\Inertia;\nuse App\\Models\\{{ model }};\n\nclass {{ model }}Controller extends Controller\n{\n    public function index() { return Inertia::render('{{ plural }}/Index', ['{{ table }}' => {{ model }}::paginate(10)]); }\n    public function create() { return Inertia::render('{{ plural }}/Create'); }\n    public function store(Request \$request) { {{ model }}::create(\$request->all()); return redirect()->route('{{ kebab }}.index'); }\n    public function show({{ model }} \${{ table }}) { return Inertia::render('{{ plural }}/Show', ['item' => \${{ table }}]); }\n    public function edit({{ model }} \${{ table }}) { return Inertia::render('{{ plural }}/Edit', ['item' => \${{ table }}]); }\n    public function update(Request \$request, {{ model }} \${{ table }}) { \${{ table }}->update(\$request->all()); return redirect()->route('{{ kebab }}.index'); }\n    public function destroy({{ model }} \${{ table }}) { \${{ table }}->delete(); return redirect()->route('{{ kebab }}.index'); }\n}\n";
            case 'request.store.stub':
                return "<?php\n\nnamespace App\\Http\\Requests;\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass Store{{ model }}Request extends FormRequest\n{\n    public function authorize(): bool\n    {\n        return true;\n    }\n\n    public function rules(): array\n    {\n        return [\n            'name' => 'required|string|max:255',\n        ];\n    }\n}\n";
            case 'request.update.stub':
                return "<?php\n\nnamespace App\\Http\\Requests;\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass Update{{ model }}Request extends FormRequest\n{\n    public function authorize(): bool\n    {\n        return true;\n    }\n\n    public function rules(): array\n    {\n        return [\n            'name' => 'required|string|max:255',\n        ];\n    }\n}\n";
            case 'routes.stub':
                return "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\{{ model }}Controller;\n\nRoute::resource('{{ kebab }}', {{ model }}Controller::class);\n";
            case 'routes.api.stub':
                return "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\API\\{{ model }}Controller;\n\nRoute::middleware(['auth:sanctum'])->group(function () {\n    Route::apiResource('{{ kebab }}', {{ model }}Controller::class);\n});\n";
            case 'routes.view.stub':
                return "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\{{ model }}Controller;\n\nRoute::middleware(['auth', 'verified'])->group(function () {\n    Route::resource('{{ kebab }}', {{ model }}Controller::class);\n});\n";
            case 'seeder.permission.stub':
                return "<?php\n\nnamespace Database\\Seeders\\Permission;\n\nuse Illuminate\\Database\\Seeder;\nuse Spatie\\Permission\\Models\\Permission;\n\nclass {{ class }} extends Seeder\n{\n    public function run(): void\n    {\n        \$permissions = ['view', 'create', 'edit', 'delete'];\n        \n        foreach (\$permissions as \$permission) {\n            Permission::firstOrCreate([\n                'name' => \"{{ permission }} {\$permission}\",\n                'guard_name' => 'web',\n            ]);\n        }\n    }\n}\n";
            default:
                return "<!-- Stub $stubPath not found -->\n";
        }
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
            $content = str_replace(
                'namespace App\\Providers;',
                "namespace App\\Providers;\n\n{$modelUse}\n{$observerUse}",
                $content
            );
        }

        // Tambah observer ke method boot()
        if (!str_contains($content, $observeLine)) {
            $content = preg_replace(
                '/public function boot\(\)(\s*): void\s*\{/',
                "public function boot()$1: void\n    {\n{$observeLine}",
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
            $this->line("📋 Model dibuat: {$name}.php");
        } else {
            $this->warn("📋 Model sudah ada: {$name}.php");
        }
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

    protected function makeController(string $name, string $plural, bool $force = false, bool $apiOnly = false, bool $viewOnly = false): void
    {
        if ($apiOnly) {
            // Only generate API controller
            $this->generateApiController($name, $plural, $force);
        } elseif ($viewOnly) {
            // Only generate Web controller
            $this->generateWebController($name, $plural, $force, $viewOnly);
        } else {
            // Generate both API and Web controllers (full-stack)
            $this->generateApiController($name, $plural, $force);
            $this->generateWebController($name, $plural, $force, false); // false = not view-only for full-stack
        }
    }

    protected function generateApiController(string $name, string $plural, bool $force = false): void
    {
        $controllerPath = app_path("Http/Controllers/API/{$name}Controller.php");

        if ($force || !$this->files->exists($controllerPath)) {
            // Ensure API directory exists
            if (!$this->files->exists(dirname($controllerPath))) {
                $this->files->makeDirectory(dirname($controllerPath), 0755, true);
            }

            $stub = $this->renderStub('controller.api.stub', [
                'model' => $name,
                'plural' => $plural,
                'table' => Str::snake($plural),
                'kebab' => Str::kebab($plural),
            ]);
            $this->files->put($controllerPath, $stub);
            $this->line("🎮 API Controller dibuat: API/{$name}Controller.php");
        } else {
            $this->warn("🎮 API Controller sudah ada: API/{$name}Controller.php");
        }
    }

    protected function generateWebController(string $name, string $plural, bool $force = false, bool $viewOnly = false): void
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");

        if ($force || !$this->files->exists($controllerPath)) {
            // Determine which stub to use - use view stub for view-only, otherwise use full-stack stub
            $stubFile = $viewOnly ? 'controller.view.stub' : 'controller.stub';

            $stub = $this->renderStub($stubFile, [
                'model' => $name,
                'plural' => $plural,
                'table' => Str::snake($plural),
                'kebab' => Str::kebab($plural),
            ]);
            $this->files->put($controllerPath, $stub);
            $this->line("🎮 Web Controller dibuat: {$name}Controller.php");
        } else {
            $this->warn("🎮 Web Controller sudah ada: {$name}Controller.php");
        }
    }

    protected function makeRequests(string $name, bool $force = false): void
    {
        $requests = [
            'Store' => 'request.store.stub',
            'Update' => 'request.update.stub'
        ];

        foreach ($requests as $type => $stubFile) {
            $requestPath = app_path("Http/Requests/{$type}{$name}Request.php");

            if ($force || !$this->files->exists($requestPath)) {
                $stub = $this->renderStub($stubFile, [
                    'model' => $name,
                    'type' => $type,
                ]);
                $this->files->put($requestPath, $stub);
                $this->line("📝 Request dibuat: {$type}{$name}Request.php");
            } else {
                $this->warn("📝 Request sudah ada: {$type}{$name}Request.php");
            }
        }
    }

    protected function makeViews(string $name, string $plural, bool $force = false): void
    {
        $table = Str::snake($plural);
        $singular = Str::singular($name);
        $viewPath = resource_path("js/pages/{$plural}");
        $views = ['Index', 'Create', 'Edit', 'Show'];

        foreach ($views as $view) {
            $filePath = "{$viewPath}/{$view}.vue";
            if ($force || !$this->files->exists($filePath)) {
                $stub = $this->renderStub("{$view}.vue.stub", [
                    'model' => $name,
                    'class' => $name,
                    'plural' => $plural,
                    'singular' => $singular,
                    'table' => $table,
                    'kebab' => Str::kebab($plural),
                    'snake' => Str::snake($plural),
                ]);
                $this->files->put($filePath, $stub);
                $this->line("🖼️ View dibuat: {$view}.vue");
            } else {
                $this->warn("🖼️ View sudah ada: {$view}.vue");
            }
        }
    }

    protected function injectRoutes(string $name, string $plural, string $kebab, bool $force = false, bool $apiOnly = false, bool $viewOnly = false): void
    {
        $variable = Str::camel($name);

        // Generate separate route files for API and Web
        if ($apiOnly) {
            // Only generate API routes
            $this->generateApiRoutes($name, $plural, $kebab, $force);
        } elseif ($viewOnly) {
            // Only generate Web routes
            $this->generateWebRoutes($name, $plural, $kebab, $force);
        } else {
            // Generate both API and Web routes (full-stack)
            $this->generateApiRoutes($name, $plural, $kebab, $force);
            $this->generateWebRoutes($name, $plural, $kebab, $force);
        }
    }

    protected function generateApiRoutes(string $name, string $plural, string $kebab, bool $force = false): void
    {
        $variable = Str::camel($name);
        $controller = "App\\Http\\Controllers\\API\\{$name}Controller";

        $content = $this->renderStub('routes.api.stub', [
            'model' => $name,
            'plural' => $plural,
            'kebab' => $kebab,
            'variable' => $variable,
            'controller' => $controller,
        ]);

        $routePath = base_path("routes/Modules/{$plural}/api.php");
        if ($force || !$this->files->exists($routePath)) {
            $this->files->put($routePath, $content);
            $this->line("🛣️ API route file dibuat: routes/Modules/{$plural}/api.php");
        } else {
            $this->warn("🛣️ API routes sudah ada: routes/Modules/{$plural}/api.php");
        }
    }

    protected function generateWebRoutes(string $name, string $plural, string $kebab, bool $force = false): void
    {
        $variable = Str::camel($name);
        $controller = "App\\Http\\Controllers\\{$name}Controller";

        $content = $this->renderStub('routes.web.stub', [
            'model' => $name,
            'plural' => $plural,
            'kebab' => $kebab,
            'variable' => $variable,
            'controller' => $controller,
        ]);

        $routePath = base_path("routes/Modules/{$plural}/web.php");
        if ($force || !$this->files->exists($routePath)) {
            $this->files->put($routePath, $content);
            $this->line("🛣️ Web route file dibuat: routes/Modules/{$plural}/web.php");
        } else {
            $this->warn("🛣️ Web routes sudah ada: routes/Modules/{$plural}/web.php");
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

    protected function ensureModulesLoader(bool $force = false): void
    {
        $webLoaderPath = base_path('routes/modules.php');
        $apiLoaderPath = base_path('routes/api-modules.php');

        // Create web modules loader
        if ($force || !$this->files->exists($webLoaderPath)) {
            $webStub = $this->renderStub('modules-loader.stub', []);
            $this->files->put($webLoaderPath, $webStub);
            $this->line("🔗 Web modules auto-loader dibuat: routes/modules.php");
        }

        // Create API modules loader
        if ($force || !$this->files->exists($apiLoaderPath)) {
            $apiStub = $this->renderStub('api-modules-loader.stub', []);
            $this->files->put($apiLoaderPath, $apiStub);
            $this->line("🔗 API modules auto-loader dibuat: routes/api-modules.php");
        }

        // Check if loaders are included in route files
        $this->suggestLoaderIntegration();
    }

    protected function suggestLoaderIntegration(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $apiRoutesPath = base_path('routes/api.php');
        $appRoutesPath = base_path('routes/app.php'); // Laravel 11+

        $webLoaderInclude = "require __DIR__ . '/modules.php';";
        $apiLoaderInclude = "require __DIR__ . '/api-modules.php';";

        // Check if web modules loader is included
        $webIncludedInWeb = false;
        $webIncludedInApp = false;

        if ($this->files->exists($webRoutesPath)) {
            $webContent = $this->files->get($webRoutesPath);
            $webIncludedInWeb = str_contains($webContent, $webLoaderInclude) ||
                str_contains($webContent, "require __DIR__ . '/modules.php'") ||
                str_contains($webContent, "require_once __DIR__ . '/modules.php'");
        }

        if ($this->files->exists($appRoutesPath)) {
            $appContent = $this->files->get($appRoutesPath);
            $webIncludedInApp = str_contains($appContent, $webLoaderInclude) ||
                str_contains($appContent, "require __DIR__ . '/modules.php'") ||
                str_contains($appContent, "require_once __DIR__ . '/modules.php'");
        }

        // Check if API modules loader is included
        $apiIncludedInApi = false;
        if ($this->files->exists($apiRoutesPath)) {
            $apiContent = $this->files->get($apiRoutesPath);
            $apiIncludedInApi = str_contains($apiContent, $apiLoaderInclude) ||
                str_contains($apiContent, "require __DIR__ . '/api-modules.php'") ||
                str_contains($apiContent, "require_once __DIR__ . '/api-modules.php'");
        }

        // Suggest integration for web routes
        if (!$webIncludedInWeb && !$webIncludedInApp) {
            $this->warn("\n⚠️  Untuk mengaktifkan auto-loading web modules, pilih salah satu:");
            $this->line("   <fg=cyan>1. Otomatis install:</>");
            $this->line("      <fg=yellow>php artisan modules:install</>");
            $this->line("");
            $this->line("   <fg=cyan>2. Manual install:</>");

            if ($this->files->exists($appRoutesPath)) {
                $this->line("      Di routes/app.php atau routes/web.php:");
            } else {
                $this->line("      Di routes/web.php:");
            }

            $this->line("      <fg=yellow>require __DIR__ . '/modules.php';</>");
            $this->line("");
        } else {
            $this->line("✅ Web modules auto-loader sudah terdaftar di routes.");
        }

        // Suggest integration for API routes
        if (!$apiIncludedInApi) {
            $this->warn("⚠️  Untuk mengaktifkan auto-loading API modules, pilih salah satu:");
            $this->line("   <fg=cyan>1. Otomatis install:</>");
            $this->line("      <fg=yellow>php artisan modules:install</>");
            $this->line("");
            $this->line("   <fg=cyan>2. Manual install:</>");
            $this->line("      Di routes/api.php:");
            $this->line("      <fg=yellow>require __DIR__ . '/api-modules.php';</>");
            $this->line("");
        } else {
            $this->line("✅ API modules auto-loader sudah terdaftar di routes/api.php.");
        }

        // Offer to auto-install if both are missing (skip in testing environment or if flag set)
        if ((!$webIncludedInWeb && !$webIncludedInApp) || !$apiIncludedInApi) {
            $this->line("");

            // Skip auto-install prompt in testing environment or if skip flag is set
            if (config('app.env') === 'testing' || $this->option('skip-install')) {
                $this->line("📝 Routes auto-loader belum terpasang. Jalankan: php artisan modules:install");
                return;
            }

            if ($this->confirm("🤔 Mau auto-install sekarang?", true)) {
                $this->call('modules:install');
            }
        }
    }
}