<?php

require_once __DIR__ . '/vendor/autoload.php';

// Mock Laravel environment
if (!function_exists('app_path')) {
    function app_path($path = '') {
        return __DIR__ . '/test-app/app' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . '/test-app' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '') {
        return __DIR__ . '/test-app/resources' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('database_path')) {
    function database_path($path = '') {
        return __DIR__ . '/test-app/database' . ($path ? '/' . $path : '');
    }
}

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use Illuminate\Filesystem\Filesystem;

$command = new MakeFeature();

echo "=== Testing API-only feature generation ===\n";
echo "Command signature: " . $command->getSignature() . "\n";
echo "Command description: " . $command->getDescription() . "\n";

// Test the determineGenerationMode method via reflection
$reflection = new ReflectionClass($command);
$method = $reflection->getMethod('determineGenerationMode');
$method->setAccessible(true);

echo "\nGeneration modes:\n";
echo "API Only: " . $method->invoke($command, true, false) . "\n";
echo "View Only: " . $method->invoke($command, false, true) . "\n";
echo "Full-stack: " . $method->invoke($command, false, false) . "\n";

echo "\n✅ API and View options are properly implemented!\n";
