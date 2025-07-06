# Changelog

All notable changes to `ngodingskuyy/laravel-module-generator` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.2.0] - 2025-07-06

### 🚀 Major Features

#### Feature Deletion System
- **NEW**: Added `delete:feature {name}` command for comprehensive feature removal
- Safely removes all generated files for a feature (models, controllers, views, migrations, etc.)
- Supports optional component cleanup (enums, observers, policies, factories, tests)
- Intelligent directory cleanup - removes empty directories after file deletion
- Interactive confirmation with detailed file listing before deletion
- Handles missing files gracefully without errors

#### Modular Routes Auto-Loader System
- **NEW**: Added automatic route loading system for modular architecture
- **NEW**: `setup:modules-loader` command to create the modules loader file
- **NEW**: `install:modules-loader` command to integrate with Laravel routing
- Auto-discovers and loads route files from `routes/modules/` directory
- Supports nested module structures with recursive loading
- Performance optimized with file existence checks
- Compatible with Laravel's route caching system

### ✨ Enhanced Commands

#### Command Improvements
- All new commands registered in service provider automatically
- Improved command descriptions and help text
- Better error handling and user feedback
- Consistent command signature patterns across all commands

#### File Generation Enhancements
- Enhanced stub rendering system with better placeholder handling
- Improved Vue component generation for modern Vue 3 patterns
- Better controller generation with proper Inertia.js integration
- Enhanced model generation with correct trait implementations

### 🧪 Comprehensive Testing

#### Test Suite Expansion
- **69 tests** with **243 assertions** (up from 37 tests, 164 assertions)
- **100% pass rate** across all test categories
- Added comprehensive unit tests for new commands
- Added feature tests for deletion and modules loader functionality
- Added integration tests for end-to-end workflows

#### New Test Categories
```
tests/
├── Feature/
│   ├── DeleteFeatureCommandTest.php          # NEW
│   ├── ModulesLoaderCommandTest.php          # NEW
│   ├── MakeFeatureCommandTest.php
│   └── MakeFeatureCommandIntegrationTest.php
├── Unit/
│   ├── DeleteFeatureCommandUnitTest.php      # NEW
│   ├── ModulesLoaderCommandUnitTest.php      # NEW
│   ├── MakeFeatureCommandUnitTest.php
│   ├── ServiceProviderTest.php
│   └── StubFilesTest.php
└── TestCase.php
```

### 📁 New File Structure

#### Modules Loader System
```
routes/
└── modules/           # Auto-loaded by modules system
    ├── feature1.php
    ├── feature2.php
    └── subfolder/
        └── nested.php
```

#### Generated Stub Files
```
src/stubs/
└── modules-loader.stub    # NEW: Template for modular route loading
```

### 🔧 Command Reference

#### New Commands Added

**Feature Deletion:**
```bash
# Delete a complete feature
php artisan delete:feature ProductManagement

# Delete with force (no confirmation)
php artisan delete:feature ProductManagement --force
```

**Modules Loader Setup:**
```bash
# Create modules loader file
php artisan setup:modules-loader

# Install modules loader into Laravel routing
php artisan install:modules-loader
```

#### Updated Commands

**Enhanced Feature Generation:**
```bash
# Create feature (existing, now more stable)
php artisan make:feature ProductManagement

# With optional components (more robust)
php artisan make:feature ProductManagement --with=enum,observer,policy,factory,test
```

### 🔄 Feature Lifecycle Management

The package now supports complete feature lifecycle:

1. **Creation**: `make:feature {name}` - Generate all feature files
2. **Management**: Manual editing and customization
3. **Deletion**: `delete:feature {name}` - Clean removal of all files
4. **Modular Routing**: Automatic route discovery and loading

### 🎯 Modular Architecture Support

#### Auto-Loading Routes
- Place route files in `routes/modules/` directory
- Routes are automatically discovered and loaded
- Supports nested directory structures
- Performance optimized with caching support

#### Integration Steps
1. Run `php artisan setup:modules-loader` to create the loader
2. Run `php artisan install:modules-loader` to integrate with Laravel
3. Place module route files in `routes/modules/`
4. Routes are automatically loaded on application boot

### 🐛 Bug Fixes

#### Command Stability
- Fixed stub rendering edge cases in MakeFeature command
- Improved error handling for missing directories
- Better validation for command parameters
- Enhanced file path resolution across different OS

#### Test Infrastructure
- Fixed test database configuration for SQLite
- Improved test isolation and cleanup
- Better mock implementations for command testing
- Enhanced assertion methods for file operations

### 📚 Documentation Updates

#### README.md Enhancements
- Added comprehensive usage examples for all commands
- Added modular routing setup guide
- Added troubleshooting section for common issues
- Updated installation instructions for v4.2

#### Development Documentation
- Updated DEVELOPMENT.md with new command development guidelines
- Added testing strategies for new features
- Updated contribution guidelines

### 🚀 Performance Improvements

#### Route Loading Optimization
- Efficient file discovery with minimal I/O operations
- Smart caching integration with Laravel's route cache
- Optimized recursive directory scanning
- Reduced memory footprint for large module collections

#### Command Execution
- Faster stub rendering with improved template processing
- Optimized file generation with batch operations
- Better memory management for large feature generation

### 🎉 Migration from v4.0 to v4.2

No breaking changes - v4.2 is fully backward compatible with v4.0:

```bash
# Update to v4.2
composer require ngodingskuyy/laravel-module-generator:^4.2

# Optional: Set up modular routing
php artisan setup:modules-loader
php artisan install:modules-loader
```

### 🙏 Credits

Special thanks to the community for feedback and suggestions that shaped v4.2!

---

## [4.0.0] - 2025-07-06log

All notable changes to `ngodingskuyy/laravel-module-generator` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.2.0] - 2025-07-06

### 🚀 Major New Features

#### Feature Deletion Command
- **NEW**: Added `module:delete {name}` command for safe feature removal
- Removes all generated files for a feature including models, controllers, views, migrations, routes, etc.
- Supports `--components` option to specify which optional components to delete
- Includes smart directory cleanup that removes empty directories after file deletion
- Comprehensive validation to prevent accidental deletion of non-generated files
- Dry-run capability with detailed output of what would be deleted

#### Modular Routes Auto-Loader System
- **NEW**: Added modular routes auto-loader (`modules.php`) for better route organization
- **NEW**: Added `modules:setup` command to create the modules.php loader file
- **NEW**: Added `modules:install` command to integrate modules loader into existing projects
- Auto-creates modules.php when generating features if it doesn't exist
- Automatically loads all `web.php` files from `routes/Modules/` directory
- Provides clean separation of feature-specific routes

### ✨ Enhanced Features

#### Improved Code Generation
- MakeFeature command now auto-creates modules.php if not present
- Better integration with the modular routes system
- Enhanced file generation with improved error handling

#### Command System Improvements
- All new commands properly registered in ServiceProvider
- Consistent command signatures and descriptions
- Improved error handling and user feedback across all commands

### 🧪 Testing Enhancements

#### Comprehensive Test Coverage
- Added `DeleteFeatureCommandTest` for feature testing of delete functionality
- Added `DeleteFeatureCommandUnitTest` for unit testing delete command logic
- Added `ModulesLoaderCommandTest` for testing modules setup and install commands
- Added `ModulesLoaderCommandUnitTest` for unit testing modules loader logic
- Updated `ServiceProviderTest` to verify registration of all new commands
- All tests pass with increased coverage: **69 tests, 243 assertions**

### 📚 Documentation Updates

#### README.md Enhancements
- Added comprehensive documentation for `module:delete` command
- Added documentation for modular routes auto-loader system
- Added usage examples for `modules:setup` and `modules:install` commands
- Updated feature overview with all new capabilities

#### Usage Examples
```bash
# Delete a feature with all its components
php artisan module:delete User

# Delete specific components only
php artisan module:delete User --components=observer,factory,test

# Setup modular routes loader
php artisan modules:setup

# Install modules loader in existing project
php artisan modules:install
```

### 📁 Updated Project Structure

#### New Commands
```
src/Commands/
├── MakeFeature.php
├── DeleteFeature.php          # NEW
├── SetupModulesLoader.php     # NEW
└── InstallModulesLoader.php   # NEW
```

#### New Stub Files
```
src/stubs/
└── modules-loader.stub        # NEW
```

#### Enhanced Test Suite
```
tests/
├── Feature/
│   ├── DeleteFeatureCommandTest.php      # NEW
│   └── ModulesLoaderCommandTest.php      # NEW
└── Unit/
    ├── DeleteFeatureCommandUnitTest.php  # NEW
    └── ModulesLoaderCommandUnitTest.php  # NEW
```

### 🔧 Technical Improvements

#### Service Provider Updates
- Registered `DeleteFeature` command
- Registered `SetupModulesLoader` command  
- Registered `InstallModulesLoader` command
- Improved command organization and loading

#### File Management
- Enhanced file path resolution and validation
- Improved directory traversal and cleanup logic
- Better error handling for file operations

### 📊 Current Statistics

- **69 tests** with **243 assertions**
- **100% pass rate**
- **4 main commands**: `make:feature`, `module:delete`, `modules:setup`, `modules:install`
- Complete CRUD feature generation with optional components
- Full feature lifecycle management (create → manage → delete)

### 🎯 Available Commands

#### Core Commands
- `make:feature {name}` - Generate complete CRUD feature
- `module:delete {name}` - Delete feature and all its components

#### Modules Management
- `modules:setup` - Create modular routes loader
- `modules:install` - Integrate modules loader into project

### 🚀 Migration from 4.0.x to 4.2.0

No breaking changes! Simply update your package:

```bash
composer update ngodingskuyy/laravel-module-generator
```

Optionally, set up the new modular routes system:
```bash
php artisan modules:setup
php artisan modules:install
```

---

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
