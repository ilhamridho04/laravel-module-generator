# Laravel Module Generator v4.2

**🚀 Laravel 12+ Focused Module Generator**

[![Tests](https://github.com/ilhamridho04/laravel-module-generator/actions/workflows/run-tests.yml/badge.svg)](https://github.com/ilhamridho04/laravel-module-generator/actions/workflows/run-tests.yml)

Modular CRUD Generator for Laravel + Vue + Tailwind (shadcn-vue) - **Optimized for Laravel 12+ with PHP 8.2+**

> **Version 4.2** is a complete refactor focused exclusively on Laravel 12+ with comprehensive testing, improved code generation, and modern best practices.

---

## 📋 Requirements

- **PHP**: ^8.2
- **Laravel**: ^12.0  
- **Spatie Laravel Permission**: ^6.0

## 📦 Installation

### Quick Installation

```bash
composer require ngodingskuyy/laravel-module-generator --dev
```

### ⚠️ If You Encounter Version Conflicts

Due to older versions on Packagist, you might need to install from source:

```bash
# Option 1: Install from GitHub (Recommended)
composer config repositories.ngodingskuyy-laravel-module-generator vcs https://github.com/ilhamridho04/laravel-module-generator
composer require ngodingskuyy/laravel-module-generator:dev-main --dev
```

```bash
# Option 2: Local development
git clone https://github.com/ilhamridho04/laravel-module-generator.git packages/laravel-module-generator
composer config repositories.local path ./packages/laravel-module-generator
composer require ngodingskuyy/laravel-module-generator:@dev --dev
```

**For detailed troubleshooting, see [DEVELOPMENT.md](DEVELOPMENT.md)**

---

## 🚀 Features

### ✨ **What's New in v4.2**

- 🎯 **Laravel 12+ Focused**: Exclusively optimized for Laravel 12+ and PHP 8.2+
- 🧪 **Comprehensive Testing**: 37 tests with 164 assertions (100% pass rate)
- 🔧 **Enhanced Code Generation**: Improved stub rendering with better error handling
- 🐛 **Bug-Free Components**: All generated files are validated and working
- 📁 **Better Project Structure**: Cleaner organization and modern conventions
- 🚀 **CI/CD Ready**: Full GitHub Actions workflow for automated testing

### � **Core Features**

- ✅ **Full CRUD Generation**: Model, migration, controller, requests, Vue components, routes, permission seeder
- 📦 **Modular Architecture**: Better separation of concerns per feature  
- 🎨 **Modern Frontend**: Vue 3 + TailwindCSS + shadcn-vue components
- 🔐 **Permission System**: Auto-generated permissions using Spatie Laravel Permission
- 🧰 **Customizable Stubs**: Fully customizable templates with intelligent fallback support
- 🔧 **Optional Components**: Generate factories, policies, observers, enums, and tests on demand

---

## � Requirements

- **PHP**: ^8.2
- **Laravel**: ^11.0
- **Spatie Laravel Permission**: ^6.0

## �📦 Installation

```bash
composer require ngodingskuyy/laravel-module-generator --dev
```

For local development/testing:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "./path/to/laravel-module-generator"
    }
  ]
}
```

Then:

```bash
composer require ngodingskuyy/laravel-module-generator:@dev
```

---

## 🔧 Usage

### Basic Usage

```bash
php artisan make:feature User
```

### With Optional Components

```bash
php artisan make:feature User --with=factory,policy,observer,enum,test
```

### Force Overwrite Existing Files

```bash
php artisan make:feature User --force
```

### 🗑️ Deleting Features

#### Delete Basic Feature

```bash
php artisan delete:feature User
```

#### Delete with Optional Components

```bash
php artisan delete:feature User --with=factory,policy,observer,enum,test
```

#### Delete All Components (including optional)

```bash
php artisan delete:feature User --all
```

#### Force Delete (no confirmation)

```bash
php artisan delete:feature User --force
```

#### What Gets Deleted

The `delete:feature` command will remove:

- **Core Files**: Model, Controller, Requests, Vue components, Routes, Migration, Permission seeder
- **Optional Components**: Enum, Observer, Policy, Factory, Test files (if specified with `--with` or `--all`)
- **Empty Directories**: Automatically cleans up empty directories after deletion
- **Service Provider**: Removes observer registration from AppServiceProvider (if applicable)

> ⚠️ **Warning**: This action is irreversible. Make sure to backup your files or use version control.

### 🔗 Auto-Loading Module Routes

#### Setup Modules Auto-Loader

```bash
php artisan modules:setup
```

#### Install and Integrate Auto-Loader

```bash
php artisan modules:install
```

#### Manual Integration

Add this line to your `routes/web.php` or `routes/app.php` (Laravel 11+):

```php
require __DIR__ . '/modules.php';
```

#### How It Works

The modules auto-loader automatically discovers and loads all `web.php` files from subdirectories in `routes/Modules/`:

```
routes/
├── web.php
├── modules.php          # Auto-loader file
└── Modules/
    ├── Users/
    │   └── web.php      # Automatically loaded
    ├── Products/
    │   └── web.php      # Automatically loaded
    └── Orders/
        └── web.php      # Automatically loaded
```

**Benefits:**
- ✅ **Automatic Discovery**: No need to manually register each module's routes
- ✅ **Laravel 11+ Compatible**: Works with both `routes/app.php` and `routes/web.php`
- ✅ **Performance**: Only loads routes for existing modules
- ✅ **Clean Organization**: Keeps routes organized by feature/module

### Generated Files Structure

Running `php artisan make:feature User` will generate:

```
📁 Generated Files:
├── app/Models/User.php                              # Eloquent Model with SoftDeletes
├── app/Http/Controllers/UserController.php         # Resource Controller
├── app/Http/Requests/StoreUserRequest.php         # Store Validation
├── app/Http/Requests/UpdateUserRequest.php        # Update Validation
├── resources/js/pages/Users/
│   ├── Index.vue                                   # List View
│   ├── Create.vue                                  # Create Form
│   ├── Edit.vue                                    # Edit Form
│   └── Show.vue                                    # Detail View
├── routes/Modules/Users/web.php                    # Module Routes
├── database/seeders/Permission/UsersPermissionSeeder.php  # Permissions
└── database/migrations/2025_xx_xx_create_users_table.php  # Migration

📁 Optional Components (with --with flag):
├── app/Factories/UserFactory.php                   # Model Factory
├── app/Policies/UserPolicy.php                     # Authorization Policy
├── app/Observers/UserObserver.php                  # Model Observer
├── app/Enums/UserStatus.php                        # Status Enum
└── tests/Feature/UserFeatureTest.php               # Feature Tests
```

---

## 📚 API Documentation

### Commands Overview

The Laravel Module Generator provides four main commands for complete feature lifecycle management:

| Command | Description | Purpose |
|---------|-------------|---------|
| `make:feature` | Generate complete CRUD feature | Create new features |
| `delete:feature` | Remove complete CRUD feature | Clean up features |
| `setup:modules-loader` | Create modular route loader | Setup route automation |
| `install:modules-loader` | Install route loader into Laravel | Integrate with Laravel routing |

---

### 📝 `make:feature` Command

**Signature:** `make:feature {name} {--with=*} {--force}`

#### Description
Generates a complete CRUD feature with all necessary files including models, controllers, views, migrations, routes, and permissions.

#### Arguments

| Argument | Type | Required | Description |
|----------|------|----------|-------------|
| `name` | string | Yes | The name of the feature to generate (PascalCase) |

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--with` | array | `[]` | Optional components to include |
| `--force` | flag | `false` | Overwrite existing files without confirmation |

#### Optional Components (`--with`)

| Component | Description | Generated Files |
|-----------|-------------|-----------------|
| `factory` | Model factory for testing | `database/factories/{Name}Factory.php` |
| `policy` | Authorization policy | `app/Policies/{Name}Policy.php` |
| `observer` | Model observer | `app/Observers/{Name}Observer.php` |
| `enum` | Status enum class | `app/Enums/{Name}StatusEnum.php` |
| `test` | Feature test class | `tests/Feature/{Name}Test.php` |

#### Generated Files (Core)

```bash
# Models
app/Models/{Name}.php

# Controllers  
app/Http/Controllers/{Name}Controller.php

# Requests
app/Http/Requests/{Name}/Store{Name}Request.php
app/Http/Requests/{Name}/Update{Name}Request.php

# Vue Components
resources/js/Pages/{Name}/Index.vue
resources/js/Pages/{Name}/Create.vue
resources/js/Pages/{Name}/Edit.vue
resources/js/Pages/{Name}/Show.vue

# Database
database/migrations/{timestamp}_create_{name}_table.php

# Routes
routes/{name}.php

# Seeders
database/seeders/{Name}PermissionSeeder.php
```

#### Usage Examples

```bash
# Basic feature generation
php artisan make:feature Product

# With optional components
php artisan make:feature Product --with=factory,policy,observer

# With all optional components
php artisan make:feature Product --with=factory,policy,observer,enum,test

# Force overwrite existing files
php artisan make:feature Product --force

# Multiple optional components (alternative syntax)
php artisan make:feature Product --with factory --with policy --with observer
```

#### Return Codes

| Code | Meaning |
|------|---------|
| `0` | Success - All files generated successfully |
| `1` | Error - Missing required arguments or validation failed |
| `2` | Error - File already exists and `--force` not specified |

---

### 🗑️ `delete:feature` Command

**Signature:** `delete:feature {name} {--with=*} {--all} {--force}`

#### Description
Safely removes all files associated with a feature, including optional components and empty directories.

#### Arguments

| Argument | Type | Required | Description |
|----------|------|----------|-------------|
| `name` | string | Yes | The name of the feature to delete (PascalCase) |

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--with` | array | `[]` | Optional components to delete |
| `--all` | flag | `false` | Delete all components (core + optional) |
| `--force` | flag | `false` | Delete without confirmation prompt |

#### Deletion Scope

**Core Files (always deleted):**
- Model, Controller, Requests
- Vue Components (Index, Create, Edit, Show)
- Migration, Routes, Permission Seeder

**Optional Files (with `--with` or `--all`):**
- Factory, Policy, Observer, Enum, Test files

**Directory Cleanup:**
- Removes empty directories after file deletion
- Maintains directory structure if other files exist

#### Usage Examples

```bash
# Delete core feature files
php artisan delete:feature Product

# Delete with specific optional components
php artisan delete:feature Product --with=factory,policy

# Delete everything (core + all optional)
php artisan delete:feature Product --all

# Force delete without confirmation
php artisan delete:feature Product --force

# Delete with confirmation showing file list
php artisan delete:feature Product --with=factory,policy,observer
```

#### Interactive Confirmation

When `--force` is not used, the command shows:
1. List of files to be deleted
2. Confirmation prompt
3. Deletion progress with status for each file

#### Return Codes

| Code | Meaning |
|------|---------|
| `0` | Success - All specified files deleted |
| `1` | Error - Feature not found or validation failed |
| `2` | Cancelled - User declined confirmation |

---

### 🔄 `setup:modules-loader` Command

**Signature:** `setup:modules-loader {--force}`

#### Description
Creates the modular route loader file that automatically discovers and loads route files from the `routes/modules/` directory.

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--force` | flag | `false` | Overwrite existing modules.php file |

#### Generated Files

```bash
routes/modules.php    # Main modules loader file
```

#### Features

- **Auto-discovery**: Recursively scans `routes/modules/` directory
- **Performance optimized**: Only loads existing files
- **Nested support**: Handles subdirectories automatically
- **Cache compatible**: Works with Laravel's route caching

#### Usage Examples

```bash
# Create modules loader
php artisan setup:modules-loader

# Force overwrite existing file
php artisan setup:modules-loader --force
```

#### Generated Code Structure

The generated `routes/modules.php` contains:
- File existence checks for performance
- Recursive directory scanning
- Automatic route file inclusion
- Error handling for missing directories

---

### ⚙️ `install:modules-loader` Command

**Signature:** `install:modules-loader {--force}`

#### Description
Integrates the modules loader into Laravel's main routing system by adding the include statement to `routes/web.php`.

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--force` | flag | `false` | Add include even if already exists |

#### Modifications

**File Modified:** `routes/web.php`

**Added Code:**
```php
// Auto-load module routes
if (file_exists(__DIR__ . '/modules.php')) {
    require __DIR__ . '/modules.php';
}
```

#### Usage Examples

```bash
# Install modules loader into Laravel routing
php artisan install:modules-loader

# Force reinstall even if already present
php artisan install:modules-loader --force
```

#### Integration Process

1. Checks if `routes/modules.php` exists
2. Scans `routes/web.php` for existing installation
3. Adds include statement if not present
4. Provides status feedback

---

### 🏗️ Module Directory Structure

After setting up the modular loader system:

```bash
routes/
├── web.php                 # Main Laravel routes (includes modules.php)
├── modules.php            # Auto-generated modules loader
└── modules/               # Your modular routes directory
    ├── products.php       # Product feature routes
    ├── users.php          # User feature routes
    ├── admin/             # Admin module subdirectory
    │   ├── dashboard.php  # Admin dashboard routes
    │   └── reports.php    # Admin reports routes
    └── api/               # API module subdirectory
        ├── v1.php         # API v1 routes
        └── v2.php         # API v2 routes
```

#### Route File Example

**`routes/modules/products.php`:**
```php
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/history', [ProductController::class, 'history'])->name('products.history');
});
```

---

### 🔧 Advanced Configuration

#### Custom Stub Files

You can publish and customize the stub templates:

```bash
# Publish stub files (if supported)
php artisan vendor:publish --tag=laravel-module-generator-stubs

# Or manually copy from:
vendor/ngodingskuyy/laravel-module-generator/src/stubs/
vendor/ngodingskuyy/laravel-module-generator/src/views/
```

#### Stub File Locations

```bash
src/stubs/
├── controller.stub          # Controller template
├── model.stub              # Model template  
├── request.store.stub      # Store request template
├── request.update.stub     # Update request template
├── migration.stub          # Migration template
├── routes.stub             # Routes template
├── seeder.permission.stub  # Permission seeder template
├── Enum.stub               # Enum template
├── Observer.stub           # Observer template
└── modules-loader.stub     # Modules loader template

src/views/
├── Index.vue.stub          # Index view template
├── Create.vue.stub         # Create view template
├── Edit.vue.stub           # Edit view template
└── Show.vue.stub           # Show view template
```

#### Environment Considerations

**Development:**
- Use `--force` flag cautiously to avoid overwriting customizations
- Test generated code before committing
- Run tests after generation: `vendor/bin/phpunit`

**Production:**
- Install as `--dev` dependency only
- Don't include in production builds
- Use route caching: `php artisan route:cache`

---

### 🧪 Testing Integration

#### Generated Test Files

When using `--with=test`, generates:

```php
// tests/Feature/{Name}Test.php
class ProductTest extends TestCase
{
    /** @test */
    public function it_can_list_products() { /* ... */ }
    
    /** @test */  
    public function it_can_create_product() { /* ... */ }
    
    /** @test */
    public function it_can_update_product() { /* ... */ }
    
    /** @test */
    public function it_can_delete_product() { /* ... */ }
}
```

#### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific feature tests
vendor/bin/phpunit tests/Feature/ProductTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

---

### 🚨 Error Handling

#### Common Issues & Solutions

**File Already Exists:**
```bash
# Solution: Use --force flag
php artisan make:feature Product --force
```

**Permission Denied:**
```bash
# Solution: Check directory permissions
chmod 755 app/Http/Controllers/
chmod 755 resources/js/Pages/
```

**Stub File Missing:**
```bash
# Solution: Reinstall package or check vendor directory
composer reinstall ngodingskuyy/laravel-module-generator
```

**Route Not Loading:**
```bash
# Solution: Check modules.php exists and is included
php artisan route:list | grep products
```

#### Debug Commands

```bash
# Check if commands are registered
php artisan list | grep "make:feature\|delete:feature\|setup:modules\|install:modules"

# Verify file generation
php artisan make:feature TestFeature --force
ls -la app/Models/TestFeature.php
php artisan delete:feature TestFeature --force
```

---

## 🧪 Comprehensive Testing (New in v4.2)

### 🎯 Test Suite Overview

Version 4.2 includes a **comprehensive test suite** with **37 tests** and **164 assertions** achieving **100% pass rate**:

#### **Unit Tests**
- `ServiceProviderTest` - Package registration and command availability
- `MakeFeatureCommandUnitTest` - Command structure and signature validation  
- `StubFilesTest` - All stub files validation and placeholder checking
- `StubRenderingTest` - Stub rendering and replacement logic

#### **Feature Tests**
- `MakeFeatureCommandTest` - File generation and content validation
- `MakeFeatureCommandIntegrationTest` - End-to-end command testing

#### **Integration Tests**
- Real Laravel app integration
- Command execution in isolated environment
- Generated file validation
- Optional component testing

### 🚀 GitHub Actions CI/CD

Automated testing pipeline with comprehensive validation:

```yaml
# .github/workflows/run-tests.yml
```

**Pipeline Features:**
- ✅ **Matrix Testing**: PHP 8.2, 8.3 × Laravel 12
- ✅ **Package Validation**: Composer.json validation, syntax checking
- ✅ **Laravel Integration**: Fresh Laravel 12 app testing
- ✅ **Command Testing**: Verify `make:feature` functionality with all options
- ✅ **File Validation**: Ensure all generated files contain correct content
- ✅ **Dependency Testing**: Validate Spatie Permission integration

### 🏃‍♂️ Local Testing

#### Run Complete Test Suite
```bash
# Run all 37 tests
vendor/bin/phpunit

# Run with detailed output
vendor/bin/phpunit --testdox

# Run specific test groups
vendor/bin/phpunit tests/Unit/
vendor/bin/phpunit tests/Feature/
```

#### Test Coverage
```bash
# Generate coverage report
vendor/bin/phpunit --coverage-html coverage-report
```

### 🔍 Test Validation

The test suite validates:

1. **Stub File Integrity**: All stubs exist and contain required placeholders
2. **Code Generation**: Generated files have correct syntax and structure  
3. **Command Options**: `--force` and `--with` options work correctly
4. **Vue Components**: All Vue files have proper template structure
5. **Database Integration**: Migrations and seeders are properly generated
6. **Permission System**: Spatie Laravel Permission integration works
7. **Package Registration**: Service provider loads correctly in Laravel

#### Run with Coverage
```bash
./vendor/bin/phpunit --coverage-html coverage-report
# or use the provided script
./coverage.sh
```

#### Testing in Real Laravel App

```bash
# Install in a test Laravel 12 project
composer create-project laravel/laravel test-app
cd test-app
composer config repositories.local path ../
composer require ngodingskuyy/laravel-module-generator:@dev

# Test the command
php artisan make:feature Product --with=test,factory,observer,enum
```

### CI Pipeline Status

The workflow tests:
1. **Package Validation** - Syntax, dependencies, composer.json
2. **Laravel Integration** - Install package in fresh Laravel 11
3. **Command Execution** - Run `make:feature` and verify file creation
4. **Laravel Compatibility** - Ensure no conflicts with Laravel core

---

## 🔄 Migration from v3.x to v4.2

### ⚠️ Breaking Changes

Version 4.2 includes breaking changes focused on Laravel 12+ support:

1. **PHP Version**: Minimum PHP 8.2 required
2. **Laravel Version**: Only Laravel 12+ supported
3. **Dependencies**: Updated to latest versions

### 📝 Migration Steps

1. **Update PHP Version**
   ```bash
   # Ensure PHP 8.2+ is installed
   php -v
   ```

2. **Update Laravel Version**
   ```bash
   # Upgrade to Laravel 12+
   composer require laravel/framework:^12.0
   ```

3. **Update Package**
   ```bash
   # Update to v4.x
   composer require ngodingskuyy/laravel-module-generator:^4.2
   ```

4. **Update Spatie Permission**
   ```bash
   # Update to v6.x if not already
   composer require spatie/laravel-permission:^6.0
   ```

5. **Clear and Rebuild**
   ```bash
   composer dump-autoload
   php artisan config:clear
   php artisan cache:clear
   ```

### 🏷️ Legacy Support

- **Laravel 8-11**: Use v3.x branch
- **PHP 7.4-8.1**: Use v3.x branch
- See [3.x documentation](https://github.com/ilhamridho04/laravel-module-generator/tree/3.x)

---

## 🤝 Contributing

We welcome contributions! Please feel free to submit a Pull Request.

### Development Setup

1. Fork the repository
2. Clone your fork: `git clone https://github.com/yourusername/laravel-module-generator`
3. Install dependencies: `composer install`
4. Run tests: `./vendor/bin/phpunit`
5. Make your changes and test thoroughly
6. Submit a pull request

### 📋 Contribution Guidelines

- Ensure all tests pass (`vendor/bin/phpunit`)
- Follow PSR-12 coding standards
- Add tests for new features
- Update documentation as needed
- Keep backwards compatibility where possible

## �📄 License

MIT © 2025 [NgodingSkuyy](https://github.com/ilhamridho04)

---

> **Laravel Module Generator v4.2** - Focused on Laravel 12+, Enhanced with Comprehensive Testing, Built for Modern PHP Development
