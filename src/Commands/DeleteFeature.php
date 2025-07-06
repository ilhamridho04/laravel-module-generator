<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class DeleteFeature extends Command
{
    protected $signature = 'delete:feature {name} {--with=* : Optional components to delete like enum, observer, policy, factory, test} {--all : Delete all related files including optional components} {--force : Delete without confirmation}';
    protected $description = 'Delete full CRUD feature and all its generated files';

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

        $this->warn("\n🗑️ Menghapus fitur: $plural ($kebab)");

        // Collect all files to be deleted
        $filesToDelete = $this->getFilesToDelete($name, $plural, $kebab, $deleteAll);

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

    protected function getFilesToDelete(string $name, string $plural, string $kebab, bool $deleteAll): array
    {
        $files = [];

        // Core files (always included)
        $coreFiles = [
            app_path("Models/{$name}.php"),
            app_path("Http/Controllers/{$name}Controller.php"),
            app_path("Http/Requests/Store{$name}Request.php"),
            app_path("Http/Requests/Update{$name}Request.php"),
            resource_path("js/pages/{$plural}/Index.vue"),
            resource_path("js/pages/{$plural}/Create.vue"),
            resource_path("js/pages/{$plural}/Edit.vue"),
            resource_path("js/pages/{$plural}/Show.vue"),
            base_path("routes/Modules/{$plural}/web.php"),
            database_path("seeders/Permission/{$plural}PermissionSeeder.php"),
        ];

        // Check which core files exist
        foreach ($coreFiles as $file) {
            if ($this->files->exists($file)) {
                $files[] = $file;
            }
        }

        // Migration files (find by pattern)
        $migrationFiles = $this->findMigrationFiles($plural);
        $files = array_merge($files, $migrationFiles);

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
            database_path("seeders/Permission"),
            app_path("Enums"),
            app_path("Observers"),
            app_path("Policies"),
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
}
