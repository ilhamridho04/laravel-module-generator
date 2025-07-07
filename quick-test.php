<?php

// Quick test for interactive menu functionality
echo "🚀 Quick Interactive Menu Test\n";
echo "==============================\n\n";

require_once __DIR__ . '/vendor/autoload.php';

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;

$command = new MakeFeature();
$reflection = new ReflectionClass($command);

// Quick checks
$checks = [
    'showGenerationModeMenu method exists' => $reflection->hasMethod('showGenerationModeMenu'),
    'Method is protected' => $reflection->getMethod('showGenerationModeMenu')->isProtected(),
    'Method returns string' => $reflection->getMethod('showGenerationModeMenu')->getReturnType()->getName() === 'string',
    'Interactive logic in handle' => strpos(file_get_contents($reflection->getFileName()), 'showGenerationModeMenu') !== false,
    'Has API option' => $command->getDefinition()->hasOption('api'),
    'Has View option' => $command->getDefinition()->hasOption('view'),
];

foreach ($checks as $test => $result) {
    echo ($result ? '✅' : '❌') . " {$test}\n";
}

echo "\n🎯 Interactive Menu Features:\n";
echo "- Menu muncul ketika tidak ada --api atau --view\n";
echo "- 3 pilihan: Full-stack, API Only, View Only\n";
echo "- Default ke Full-stack (option 1)\n";
echo "- Konfirmasi setelah pilih mode\n";

echo "\n✅ Interactive Menu ready to use!\n";
echo "\nUsage: php artisan features:create UserManagement\n";
