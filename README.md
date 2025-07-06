# Laravel Module Generator
## 📦 Installation## 📋 Requirements

- **PHP**: ^8.1
- **Laravel**: ^9.0, ^10.0, ^11.0
- **Spatie Laravel Permission**: ^5.11 | ^6.0## Quick Installation

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

**For detailed troubleshooting, see [DEVELOPMENT.md](DEVELOPMENT.md)**//github.com/ilhamridho04/laravel-module-generator/actions/workflows/run-tests.yml/badge.svg)

A modular Laravel + Vue 3 + TailwindCSS + shadcn-vue CRUD feature generator.

Created by **NgodingSkuyy** to accelerate development using standardized full-stack architecture.

---

## 🚀 Features

- ✅ **Full CRUD Generation**: Model, migration, controller, requests, Vue components, routes, permission seeder
- 📦 **Modular Architecture**: Better separation of concerns per feature
- 🎨 **Modern Frontend**: Vue 3 + TailwindCSS + shadcn-vue components
- 🔐 **Permission System**: Auto-generated permissions using Spatie Laravel Permission
- 🧰 **Customizable Stubs**: Fully customizable templates with fallback support
- 🚀 **Multi-Laravel Support**: Compatible with Laravel 8.12+ through Laravel 12
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

## 🧩 Customizing Stubs

### Publishing Stubs

Publish stub files to customize templates:

```bash
php artisan vendor:publish --tag=laravel-module-generator-stubs
```

This will copy stubs to:
```
stubs/laravel-module-generator/
├── model.stub
├── controller.stub
├── request.store.stub
├── request.update.stub
├── routes.stub
├── seeder.permission.stub
├── Index.vue.stub
├── Create.vue.stub
├── Edit.vue.stub
├── Show.vue.stub
├── Enum.stub
└── Observer.stub
```

### Fallback System

The package includes a robust fallback system:
- **Custom stubs** (in `stubs/laravel-module-generator/`) take priority
- **Default stubs** (in package) are used as fallback
- **Basic templates** are generated if no stubs exist

### Customization Examples

Modify stubs to fit your coding style:
- **Validation rules** in request stubs
- **UI components** in Vue stubs
- **Model relationships** in model stub
- **Route patterns** in routes stub

---

## 🧪 Testing & CI

### GitHub Actions Workflow

This package includes a comprehensive CI/CD pipeline:

```yaml
.github/workflows/run-tests.yml
```

**Features:**
- ✅ **Multi-version testing**: PHP 8.2, 8.3 × Laravel 11
- ✅ **Package validation**: Composer.json validation, syntax checking
- ✅ **Laravel integration**: Real Laravel app testing
- ✅ **Command testing**: Verify `make:feature` functionality
- ✅ **File generation**: Validate all generated files

### Local Testing

#### Run Package Tests
```bash
./vendor/bin/phpunit
```

#### Run with Coverage
```bash
./vendor/bin/phpunit --coverage-html coverage-report
# or use the provided script
./coverage.sh
```

#### Test in Laravel App
```bash
# Install in a test Laravel project
composer create-project laravel/laravel test-app
cd test-app
composer config repositories.local path ../
composer require ngodingskuyy/laravel-module-generator:@dev

# Test the command
php artisan make:feature Product --with=test,factory
```

### CI Pipeline Status

The workflow tests:
1. **Package Validation** - Syntax, dependencies, composer.json
2. **Laravel Integration** - Install package in fresh Laravel 11
3. **Command Execution** - Run `make:feature` and verify file creation
4. **Laravel Compatibility** - Ensure no conflicts with Laravel core

---

## � Roadmap

- [ ] **Livewire Support**: Generate Livewire components alongside Vue
- [ ] **API Resources**: Generate API controllers and resources
- [ ] **Database Relationships**: Auto-detect and generate relationships
- [ ] **Frontend Framework Options**: Support for React, Alpine.js
- [ ] **Advanced Testing**: Generate comprehensive test suites
- [ ] **Documentation Generator**: Auto-generate API documentation

## 🤝 Contributing

We welcome contributions! Please feel free to submit a Pull Request.

### Development Setup

1. Fork the repository
2. Clone your fork: `git clone https://github.com/yourusername/laravel-module-generator`
3. Install dependencies: `composer install`
4. Run tests: `./vendor/bin/phpunit`
5. Make your changes and test thoroughly
6. Submit a pull request

## �📄 License

MIT © 2025 [NgodingSkuyy](https://github.com/ilhamridho04)

---

**Built with ❤️ by NgodingSkuyy**
