<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class DeleteFeature extends Command
{
    protected $signature = 'module:delete {name} 
                            {--with=* : Optional components to delete like enum, observer, policy, factory, test} 
                            {--all : Delete all related files including optional components} 
                            {--force : Delete without confirmation}
                            {--api : Delete API-only components}
                            {--view : Delete View-only components}';
    protected $description = 'Delete full CRUD feature and all its generated files with interactive mode selection';

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
        $deleteAll = $this->option('all');
        $apiOnly = $this->option('api');
        $viewOnly = $this->option('view');

        // Validate options
        if ($apiOnly && $viewOnly) {
            $this->error('❌ Tidak bisa menggunakan --api dan --view bersamaan. Pilih salah satu atau kosongkan untuk full deletion.');
            return;
        }

        // If no mode is specified via options, show interactive menu
        if (!$apiOnly && !$viewOnly) {
            $deletionMode = $this->showDeletionModeMenu();

            switch ($deletionMode) {
                case 'api':
                    $apiOnly = true;
                    break;
                case 'view':
                    $viewOnly = true;
                    break;
                case 'full':
                default:
                    // Full deletion mode (default behavior) - keep both flags as false
                    break;
            }
        }

        // Determine deletion mode
        $mode = $this->determineDeletionMode($apiOnly, $viewOnly);
        $this->warn("\n🗑️ Menghapus fitur: $plural ($kebab) - Mode: {$mode}");

        // Collect all files to be deleted based on mode
        $filesToDelete = $this->getFilesToDelete($name, $plural, $kebab, $deleteAll, $apiOnly, $viewOnly);

        if (empty($filesToDelete)) {
            $this->info("❌ Tidak ada file yang ditemukan untuk fitur: $plural");
            return;
        }

        // Show files that will be deleted
        $this->showFilesToDelete($filesToDelete);

        // Confirm deletion
        if (!$force && !$this->confirm("⚠️ Apakah Anda yakin ingin menghapus semua file ini?")) {
            $this->info("❌ Penghapusan dibatalkan.");
            return;
        }

        // Delete files
        $deletedCount = $this->deleteFiles($filesToDelete);

        // Clean up empty directories
        $this->cleanupEmptyDirectories($name, $plural);

        $this->info("\n✅ Fitur $plural berhasil dihapus! ($deletedCount file dihapus)");
    }

    protected function getFilesToDelete(string $name, string $plural, string $kebab, bool $deleteAll, bool $apiOnly = false, bool $viewOnly = false): array
    {
        $files = [];

        // Core files based on mode
        if ($apiOnly) {
            // Only delete API-related files
            $coreFiles = [
                app_path("Http/Controllers/API/{$name}Controller.php"),
                base_path("routes/Modules/{$plural}/api.php"),
            ];
        } elseif ($viewOnly) {
            // Only delete View-related files
            $coreFiles = [
                app_path("Http/Controllers/{$name}Controller.php"),
                base_path("routes/Modules/{$plural}/web.php"),
                resource_path("js/pages/{$plural}/Index.vue"),
                resource_path("js/pages/{$plural}/Create.vue"),
                resource_path("js/pages/{$plural}/Edit.vue"),
                resource_path("js/pages/{$plural}/Show.vue"),
            ];
        } else {
            // Full deletion - all files
            $coreFiles = [
                app_path("Models/{$name}.php"),
                app_path("Http/Controllers/{$name}Controller.php"),
                app_path("Http/Controllers/API/{$name}Controller.php"),
                app_path("Http/Requests/Store{$name}Request.php"),
                app_path("Http/Requests/Update{$name}Request.php"),
                resource_path("js/pages/{$plural}/Index.vue"),
                resource_path("js/pages/{$plural}/Create.vue"),
                resource_path("js/pages/{$plural}/Edit.vue"),
                resource_path("js/pages/{$plural}/Show.vue"),
                base_path("routes/Modules/{$plural}/api.php"),
                base_path("routes/Modules/{$plural}/web.php"),
                database_path("seeders/Permission/{$plural}PermissionSeeder.php"),
                app_path("Enums/{$name}Status.php"),
                app_path("Observers/{$name}Observer.php"),
                app_path("Policies/{$name}Policy.php"),
                // base_path("Traits/ApiResponser.php"),
            ];
        }

        // Check which core files exist
        foreach ($coreFiles as $file) {
            if ($this->files->exists($file)) {
                $files[] = $file;

                // If deleting an Observer, also remove its registration from AppServiceProvider
                if (
                    Str::endsWith($file, 'Observer.php') &&
                    (Str::contains($file, 'Observers') || Str::contains($file, 'Observer'))
                ) {
                    $this->removeObserverFromServiceProvider($name);
                }
            }
        }

        // Handle ApiResponser trait for API-related modes (API only or full deletion)
        if ($apiOnly || (!$apiOnly && !$viewOnly)) {
            $apiResponserPath = app_path('Traits/ApiResponser.php');
            if ($this->files->exists($apiResponserPath) && $this->shouldDeleteApiResponser()) {
                $files[] = $apiResponserPath;
            }
        }

        // Migration files (only for full deletion or when explicitly requested)
        if (!$apiOnly && !$viewOnly) {
            $migrationFiles = $this->findMigrationFiles($plural);
            $files = array_merge($files, $migrationFiles);
        }

        // Optional components
        $optionalFiles = $this->getOptionalFiles($name, $plural, $deleteAll);
        $files = array_merge($files, $optionalFiles);

        return $files;
    }

    protected function findMigrationFiles(string $plural): array
    {
        $table = Str::snake($plural);
        $migrationPath = database_path('migrations');
        $files = [];

        if ($this->files->exists($migrationPath)) {
            $migrationFiles = $this->files->files($migrationPath);
            foreach ($migrationFiles as $file) {
                $filename = $file->getFilename();
                if (str_contains($filename, "create_{$table}_table")) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }

    protected function getOptionalFiles(string $name, string $plural, bool $deleteAll): array
    {
        $files = [];
        $options = collect($this->option('with'));

        // If --all is specified, include all optional components
        if ($deleteAll) {
            $options = collect(['enum', 'observer', 'policy', 'factory', 'test']);
        }

        if ($options->contains('enum')) {
            $enumPath = app_path("Enums/{$name}Status.php");
            if ($this->files->exists($enumPath)) {
                $files[] = $enumPath;
            }
        }

        if ($options->contains('observer')) {
            $observerPath = app_path("Observers/{$name}Observer.php");
            if ($this->files->exists($observerPath)) {
                $files[] = $observerPath;
            }
        }

        if ($options->contains('policy')) {
            $policyPath = app_path("Policies/{$name}Policy.php");
            if ($this->files->exists($policyPath)) {
                $files[] = $policyPath;
            }
        }

        if ($options->contains('factory')) {
            $factoryPath = database_path("factories/{$name}Factory.php");
            if ($this->files->exists($factoryPath)) {
                $files[] = $factoryPath;
            }
        }

        if ($options->contains('test')) {
            $testPath = base_path("tests/Feature/{$name}FeatureTest.php");
            if ($this->files->exists($testPath)) {
                $files[] = $testPath;
            }
        }

        return $files;
    }

    protected function showFilesToDelete(array $files): void
    {
        $this->info("\n📋 File yang akan dihapus:");
        foreach ($files as $file) {
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
            $this->line("  🗑️  $relativePath");
        }
        $this->line("");
    }

    protected function deleteFiles(array $files): int
    {
        $deletedCount = 0;

        foreach ($files as $file) {
            if ($this->files->exists($file)) {
                $this->files->delete($file);
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
                $this->line("✅ Dihapus: $relativePath");
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    protected function cleanupEmptyDirectories(string $name, string $plural): void
    {
        $directoriesToCheck = [
            resource_path("js/pages/{$plural}"),
            base_path("routes/Modules/{$plural}"),
            app_path("Http/Controllers/API"), // Check if API controller directory is empty
            database_path("seeders/Permission"),
            app_path("Enums"),
            app_path("Observers"),
            app_path("Policies"),
            app_path("Traits"), // Check if Traits directory is empty
        ];

        foreach ($directoriesToCheck as $directory) {
            if ($this->files->exists($directory) && $this->isDirectoryEmpty($directory)) {
                $this->files->deleteDirectory($directory);
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $directory);
                $this->line("🗂️  Direktori kosong dihapus: $relativePath");
            }
        }

        // Clean up parent directories if empty
        $parentDirectories = [
            resource_path("js/pages"),
            base_path("routes/Modules"),
            database_path("seeders"),
            app_path("Http/Controllers"), // Only if Controllers directory becomes empty (unlikely)
        ];

        foreach ($parentDirectories as $directory) {
            if ($this->files->exists($directory) && $this->isDirectoryEmpty($directory)) {
                $this->files->deleteDirectory($directory);
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $directory);
                $this->line("🗂️  Direktori parent kosong dihapus: $relativePath");
            }
        }
    }

    protected function isDirectoryEmpty(string $directory): bool
    {
        if (!$this->files->exists($directory)) {
            return true;
        }

        $contents = $this->files->allFiles($directory);
        $directories = $this->files->directories($directory);

        return empty($contents) && empty($directories);
    }

    protected function removeObserverFromServiceProvider(string $name): void
    {
        $providerPath = app_path('Providers/AppServiceProvider.php');
        if (!$this->files->exists($providerPath)) {
            return;
        }

        $content = $this->files->get($providerPath);
        $modelUse = "use App\\Models\\{$name};";
        $observerUse = "use App\\Observers\\{$name}Observer;";
        $observeLine = "        {$name}::observe({$name}Observer::class);";

        // Remove observer registration
        if (str_contains($content, $observeLine)) {
            $content = str_replace($observeLine, '', $content);
            $content = str_replace($modelUse, '', $content);
            $content = str_replace($observerUse, '', $content);

            // Clean up empty lines
            $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);

            $this->files->put($providerPath, $content);
            $this->line("✅ Observer {$name}Observer dihapus dari AppServiceProvider");
        }
    }

    protected function determineDeletionMode(bool $apiOnly, bool $viewOnly): string
    {
        if ($apiOnly) {
            return 'API Only';
        }

        if ($viewOnly) {
            return 'View Only';
        }

        return 'Full Deletion (API + View)';
    }

    protected function showDeletionModeMenu(): string
    {
        $this->info("\n🎯 Pilih mode penghapusan fitur:");
        $this->line("   <fg=cyan>1.</> Full Deletion (API + Views) - Hapus semua controller, routes, views");
        $this->line("   <fg=yellow>2.</> API Only - Hapus hanya API controller dan routes");
        $this->line("   <fg=green>3.</> View Only - Hapus hanya Vue views dan web controller");
        $this->line("");

        $choice = $this->choice(
            '🤔 Pilih mode deletion',
            [
                '1' => 'Full Deletion (API + Views)',
                '2' => 'API Only',
                '3' => 'View Only'
            ],
            '1' // Default to full deletion
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
                $this->line("   <fg=cyan>✅ Mode Full Deletion dipilih</>");
                return 'full';
        }
    }

    protected function shouldDeleteApiResponser(): bool
    {
        $apiControllerPath = app_path('Http/Controllers/API');

        if (!$this->files->exists($apiControllerPath)) {
            return true; // No API directory, safe to delete
        }

        $apiControllers = $this->files->files($apiControllerPath);

        // If there are other API controllers besides the one being deleted, keep the trait
        return count($apiControllers) <= 1;
    }
}
