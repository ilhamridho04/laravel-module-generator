<?php

require_once __DIR__ . '/vendor/autoload.php';

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

// Setup Laravel container
$app = new Container();
$app->singleton('files', function () {
    return new Filesystem();
});

$app->singleton('events', function () {
    return new Dispatcher();
});

// Create console application
$console = new Application();
$command = new MakeFeature();
$command->setLaravel($app);
$console->add($command);

// Test API controller generation
echo "🧪 Testing API Controller Permission Implementation...\n";

$input = new ArrayInput([
    'command' => 'module:create',
    'name' => 'TestProduct',
    '--api' => true,
    '--force' => true
]);

$output = new BufferedOutput();

try {
    $console->run($input, $output);
    echo "✅ Command executed successfully\n";
    echo $output->fetch();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🔍 Checking generated files for permission middleware...\n";

// Check if controller was generated with permission middleware
$controllerPath = __DIR__ . '/workbench/app/Http/Controllers/API/TestProductController.php';

if (file_exists($controllerPath)) {
    echo "✅ API Controller file exists\n";
    
    $content = file_get_contents($controllerPath);
    
    // Check for permission middleware
    $permissionChecks = [
        "permission:view test_products" => "View permission middleware",
        "permission:create test_products" => "Create permission middleware", 
        "permission:update test_products" => "Update permission middleware",
        "permission:delete test_products" => "Delete permission middleware",
        "->only(['index', 'show'])" => "View methods constraint",
        "->only(['store'])" => "Create methods constraint",
        "->only(['update'])" => "Update methods constraint", 
        "->only(['destroy'])" => "Delete methods constraint"
    ];
    
    foreach ($permissionChecks as $check => $description) {
        if (strpos($content, $check) !== false) {
            echo "✅ $description found\n";
        } else {
            echo "❌ $description NOT found\n";
        }
    }
    
    echo "\n📄 Generated Controller Content Preview:\n";
    echo substr($content, 0, 800) . "...\n";
    
} else {
    echo "❌ API Controller file NOT found at: $controllerPath\n";
}

// Check permission seeder
$seederPath = __DIR__ . '/workbench/database/seeders/Permission/TestProductsPermissionSeeder.php';

if (file_exists($seederPath)) {
    echo "\n✅ Permission Seeder file exists\n";
    
    $seederContent = file_get_contents($seederPath);
    
    if (strpos($seederContent, 'test_products') !== false) {
        echo "✅ Permission seeder contains correct permission name\n";
    } else {
        echo "❌ Permission seeder does NOT contain correct permission name\n";
    }
    
    echo "\n📄 Generated Seeder Content:\n";
    echo $seederContent . "\n";
    
} else {
    echo "\n❌ Permission Seeder file NOT found at: $seederPath\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🧹 Cleaning up test files...\n";

// Cleanup
$filesToClean = [
    $controllerPath,
    $seederPath,
    __DIR__ . '/workbench/app/Models/TestProduct.php',
    __DIR__ . '/workbench/app/Http/Requests/StoreTestProductRequest.php',
    __DIR__ . '/workbench/app/Http/Requests/UpdateTestProductRequest.php'
];

foreach ($filesToClean as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "🗑️ Deleted: " . basename($file) . "\n";
    }
}

echo "✅ Test completed!\n";
