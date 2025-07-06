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
