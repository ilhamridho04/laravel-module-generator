# Laravel Module Generator - Local Development Guide

## 🚨 Installation Issues & Solutions

### Problem: Version Conflicts

If you encounter dependency conflicts like this:

```
ngodingskuyy/laravel-module-generator v1.0.0 requires spatie/laravel-permission ^5.11
```

This happens because older versions are published on Packagist. Here are the solutions:

### Solution 1: Force Latest Version (Recommended)

```bash
# Remove old version if exists
composer remove ngodingskuyy/laravel-module-generator

# Install latest version from repository
composer config repositories.ngodingskuyy-laravel-module-generator vcs https://github.com/ilhamridho04/laravel-module-generator
composer require ngodingskuyy/laravel-module-generator:dev-main --dev
```

### Solution 2: Local Path Installation

```bash
# Clone the repository
git clone https://github.com/ilhamridho04/laravel-module-generator.git packages/laravel-module-generator

# Add to composer.json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/laravel-module-generator",
            "options": {
                "symlink": false
            }
        }
    ]
}

# Install
composer require ngodingskuyy/laravel-module-generator:@dev --dev
```

### Solution 3: Override Version Constraints

Add to your `composer.json`:

```json
{
    "require-dev": {
        "ngodingskuyy/laravel-module-generator": "dev-main"
    },
    "config": {
        "allow-plugins": {
            "pestphp/pest-plugin": true
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

### Solution 4: Laravel 11 Specific

If you're using Laravel 11 and want to avoid Laravel 12 conflicts:

```bash
# Lock Laravel to v11
composer require "laravel/framework:^11.0" --no-update
composer require ngodingskuyy/laravel-module-generator:dev-main --dev
```

## 🔧 Development Dependencies

This package requires:
- PHP ^8.2
- Laravel ^11.0
- Spatie Laravel Permission ^6.0

## 🧪 Testing

```bash
# Run tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

## 🚀 Usage After Installation

```bash
# Generate a feature module
php artisan make:feature Product

# With optional components
php artisan make:feature Product --with=factory,policy,observer,enum,test

# Force overwrite
php artisan make:feature Product --force
```

## 📦 Publishing New Version

When you're ready to publish a new version to Packagist:

1. Tag the release:
```bash
git tag v3.0.0
git push origin v3.0.0
```

2. Update Packagist webhook or manually update on packagist.org

This will resolve the version conflicts with older published versions.
