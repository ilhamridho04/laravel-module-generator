<?php

require __DIR__ . '/vendor/autoload.php';

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use Illuminate\Console\Application;
use Illuminate\Filesystem\Filesystem;

// Mock Laravel app environment
if (!function_exists('app_path')) {
    function app_path($path = '') {
        return __DIR__ . '/test-app/app' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . '/test-app' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '') {
        return __DIR__ . '/test-app/resources' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('database_path')) {
    function database_path($path = '') {
        return __DIR__ . '/test-app/database' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

// Create console application
$console = new Application('Laravel Module Generator Test', '1.0.0');
$command = new MakeFeature();
$console->add($command);

echo "Testing command signature...\n";
echo "Command name: " . $command->getName() . "\n";
echo "Command signature: " . $command->getDefinition()->getSynopsis() . "\n";

// Check if name argument is optional
$arguments = $command->getDefinition()->getArguments();
foreach ($arguments as $argument) {
    echo "Argument: " . $argument->getName() . " - Required: " . ($argument->isRequired() ? 'Yes' : 'No') . "\n";
}
