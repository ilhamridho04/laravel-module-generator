# Changelog

All notable changes to `ngodingskuyy/laravel-module-generator` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.0.0] - 2025-07-06

### 🚀 Major Changes

#### Laravel 12+ Focus
- **BREAKING**: Dropped support for Laravel versions below 12.0
- **BREAKING**: Minimum PHP version now 8.2
- Updated all dependencies to support Laravel 12+
- Set branch alias to `4.x-dev` for Laravel 12+ compatibility

#### Dependencies Update
- Updated Laravel framework requirement to `^12.0`
- Updated PHP requirement to `^8.2`
- Updated Spatie Laravel Permission to `^6.0`
- Updated Orchestra Testbench to `^10.0` for Laravel 12 compatibility
- Updated PHPUnit to `^11.5` for PHP 8.2+ compatibility

### ✨ New Features

#### Comprehensive Test Suite
- Added comprehensive unit tests for all components
- Added feature tests for command functionality
- Added integration tests for end-to-end validation
- Added stub validation tests to ensure template integrity
- All tests pass with 100% success rate (37 tests, 164 assertions)

#### Enhanced Code Generation
- Improved stub rendering system with fallback support
- Better placeholder replacement with `{{ variable }}` format
- Enhanced Vue component generation for modern Vue 3
- Improved controller generation with proper Inertia.js integration

### 🐛 Bug Fixes

#### Command Fixes
- Fixed `--force` option handling for sub-commands
- Removed unsupported `--force` option from `make:factory` and `make:policy` commands
- Fixed command signature accessibility in unit tests using reflection
- Improved error handling for missing stub files

#### Stub File Improvements
- Removed header comments from all stub files that were being included in generated code
- Fixed Vue component stub loading from correct `views/` directory
- Fixed controller stub to generate proper method signatures
- Fixed model stub to use correct trait syntax (`use HasFactory, SoftDeletes;`)
- Fixed migration stub with proper content structure

#### File Generation
- Fixed Vue component file generation with correct template structure
- Fixed controller generation with proper method signatures
- Fixed model generation with correct namespace and traits
- Fixed request validation class generation

### 🔧 Infrastructure Improvements

#### CI/CD Updates
- Updated GitHub Actions workflow for Laravel 12+ only
- Matrix testing for PHP 8.2 and 8.3 with Laravel 12
- Improved test workflow with proper Laravel project setup
- Added comprehensive dependency installation steps

#### Configuration Updates
- Updated PHPUnit configuration for SQLite in-memory database
- Updated test environment setup for Laravel 12
- Improved package discovery configuration

#### Documentation
- Updated README.md with Laravel 12+ requirements
- Updated DEVELOPMENT.md with new development guidelines
- Updated installation instructions for v4.x
- Added troubleshooting section for Laravel 12+ specific issues

### 📁 Project Structure

#### Test Organization
```
tests/
├── Feature/
│   ├── MakeFeatureCommandIntegrationTest.php
│   └── MakeFeatureCommandTest.php
├── Unit/
│   ├── MakeFeatureCommandUnitTest.php
│   ├── ServiceProviderTest.php
│   ├── StubFilesTest.php
│   └── StubRenderingTest.php
└── TestCase.php
```

#### Stub Files Structure
```
src/
├── stubs/
│   ├── controller.stub
│   ├── Enum.stub
│   ├── migration.stub
│   ├── model.stub
│   ├── Observer.stub
│   ├── request.store.stub
│   ├── request.update.stub
│   ├── routes.stub
│   └── seeder.permission.stub
└── views/
    ├── Create.vue.stub
    ├── Edit.vue.stub
    ├── Index.vue.stub
    └── Show.vue.stub
```

### 🎯 Generated Components

The `make:feature {name}` command now generates:

- **Model** with proper traits, fillable fields, and scope methods
- **Controller** with full CRUD operations and Inertia.js integration
- **Request Classes** for store and update validation
- **Vue Components** for Index, Create, Edit, and Show views
- **Migration** with proper table structure
- **Routes** with resource routing
- **Permission Seeder** for Spatie Laravel Permission integration

#### Optional Components (with `--with` flag):
- **Enum** classes for status management
- **Observer** classes with automatic registration
- **Policy** classes for authorization
- **Factory** classes for testing
- **Test** classes for feature testing

### 🔄 Migration Guide

#### From v3.x to v4.x

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
   composer require ngodingskuyy/laravel-module-generator:^4.0
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

### 🧪 Testing

Run the test suite:
```bash
# Run all tests
vendor/bin/phpunit

# Run with detailed output
vendor/bin/phpunit --testdox

# Run specific test groups
vendor/bin/phpunit tests/Unit/
vendor/bin/phpunit tests/Feature/
```

### 📊 Test Coverage

- **37 tests** with **164 assertions**
- **100% pass rate**
- Covers unit, feature, and integration testing
- Validates stub file integrity and code generation

### 🙏 Credits

Thanks to all contributors who helped make this Laravel 12+ focused version possible!

---

## [3.x] - Previous Versions

For changelog of previous versions supporting Laravel 8-11, please refer to the git history or previous releases.

### Legacy Support

- Laravel 8-11 support available in 3.x branch
- PHP 7.4-8.1 support available in 3.x branch
- See [3.x branch](https://github.com/ilhamridho04/laravel-module-generator/tree/3.x) for legacy documentation
