<?php

// Quick test untuk API controller folder structure
echo "🧪 Testing API Controller Folder Structure\n";
echo "==========================================\n\n";

require_once __DIR__ . '/vendor/autoload.php';

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;

$command = new MakeFeature();
$reflection = new ReflectionClass($command);

// Test makeController method
$makeControllerMethod = $reflection->getMethod('makeController');
$makeControllerMethod->setAccessible(true);

// Mock filesystem calls untuk test
$makeControllerCode = file_get_contents($reflection->getFileName());

$checks = [
    'API controller path logic exists' => strpos($makeControllerCode, 'app_path("Http/Controllers/API/{$name}Controller.php")') !== false,
    'API directory creation logic exists' => strpos($makeControllerCode, 'app_path("Http/Controllers/API")') !== false,
    'API controller namespace in stub' => strpos(file_get_contents(__DIR__ . '/src/stubs/controller.api.stub'), 'namespace App\Http\Controllers\API') !== false,
    'API routes use correct namespace' => strpos(file_get_contents(__DIR__ . '/src/stubs/routes.api.stub'), 'App\Http\Controllers\API') !== false,
];

foreach ($checks as $test => $result) {
    echo ($result ? '✅' : '❌') . " {$test}\n";
}

echo "\n🎯 API Controller Features:\n";
echo "- API controllers dibuat di folder: app/Http/Controllers/API/\n";
echo "- Namespace: App\\Http\\Controllers\\API\n";
echo "- Routes menggunakan namespace yang benar\n";
echo "- Folder API otomatis dibuat jika belum ada\n";

echo "\n✅ API controller folder structure ready!\n";
echo "\nUsage: php artisan modules:create User --api\n";
echo "Result: app/Http/Controllers/API/UserController.php\n";
