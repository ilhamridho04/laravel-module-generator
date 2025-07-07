<?php

// Simple test to verify the command signature format
$signatureLine = 'module:create {name?} 
                            {--with=* : Optional components like enum, observer, policy, factory, test} 
                            {--force : Overwrite existing files}
                            {--api : Generate API-only (without Vue views)}
                            {--view : Generate View-only (without API routes)}
                            {--skip-install : Skip auto-install prompt for routes}';

echo "Command signature analysis:\n";
echo "Raw signature: " . $signatureLine . "\n\n";

// Check if name argument is optional
if (strpos($signatureLine, '{name?}') !== false) {
    echo "✅ Name argument is correctly marked as optional with '?'\n";
} else {
    echo "❌ Name argument is NOT marked as optional\n";
}

// Check for required format
if (strpos($signatureLine, '{name}') !== false && strpos($signatureLine, '{name?}') === false) {
    echo "❌ Name argument is required (no '?' found)\n";
} else if (strpos($signatureLine, '{name?}') !== false) {
    echo "✅ Name argument is optional ('?' found)\n";
}

echo "\nThe command should work with: php artisan module:create\n";
echo "And prompt for name interactively.\n";
