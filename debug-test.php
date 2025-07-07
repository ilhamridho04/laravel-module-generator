<?php

require_once 'vendor/autoload.php';

// Simple test to see what gets created
echo "Testing what gets created with basic feature...\n";

// Mock the necessary Laravel functions for basic testing
if (!function_exists('app_path')) {
    function app_path($path = '') {
        return __DIR__ . '/test-output/app' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . '/test-output' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '') {
        return __DIR__ . '/test-output/resources' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('database_path')) {
    function database_path($path = '') {
        return __DIR__ . '/test-output/database' . ($path ? '/' . $path : '');
    }
}

// Check if enum file would be created
$enumPath = app_path("Enums/TestStatus.php");
echo "Enum path would be: $enumPath\n";

// Check if the file exists (it shouldn't for this test)
if (file_exists($enumPath)) {
    echo "❌ Enum file exists when it shouldn't\n";
} else {
    echo "✅ Enum file does not exist (correct)\n";
}

echo "Test completed.\n";
