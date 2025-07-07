# Changelog

All notable changes to `ngodingskuyy/laravel-module-generator` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.5.0] - 2025-07-08

### 🚀 NEW: Smart Routes Auto-Installation

#### Enhanced Commands
- **NEW**: `modules:setup` command creates both web and API route loaders
- **NEW**: `modules:install` command auto-integrates routes into Laravel routes files
- **SMART**: Auto-detect Laravel 11+ (`routes/app.php` vs `routes/web.php`)
- **SAFE**: Backup protection prevents accidental overwrites

#### Auto-Installation Integration
- **INTERACTIVE**: `modules:create` automatically detects if routes need installation
- **USER-FRIENDLY**: Offers one-click auto-installation with confirmation
- **INTELLIGENT**: Checks both web and API routes installation status
- **FLEXIBLE**: Supports manual installation instructions as fallback

#### Complete Routes Separation
- **ORGANIZED**: Web routes go to `routes/modules.php` → `routes/web.php`
- **ORGANIZED**: API routes go to `routes/api-modules.php` → `routes/api.php`
- **CLEAN**: No more mixed routes in single files
- **STRUCTURED**: Each feature has separate `web.php` and `api.php` files

#### Routes Structure
```
routes/
├── web.php              # + require __DIR__ . '/modules.php';
├── api.php              # + require __DIR__ . '/api-modules.php';
├── modules.php          # Web modules auto-loader
├── api-modules.php      # API modules auto-loader
└── Modules/
    └── FeatureName/
        ├── web.php      # Web routes with auth middleware
        └── api.php      # API routes with auth:sanctum middleware
```

#### Command Examples
```bash
# One-command setup (recommended)
php artisan modules:install

# Manual setup
php artisan modules:setup
# Then manually add requires to routes files

# Auto-install during feature creation
php artisan modules:create Product
# System will offer auto-install if not detected
```

### 🔧 Enhanced Route Templates

#### Improved Route Stubs
- **FIXED**: All route stubs now use consistent `{{ model }}Controller` placeholder
- **CLEAN**: Simplified route definitions using `Route::resource()` and `Route::apiResource()`
- **ORGANIZED**: Separate stubs for web (`routes.web.stub`) and API (`routes.api.stub`)
- **BACKWARD-COMPATIBLE**: Old `routes.stub` maintained for compatibility

#### Updated Route Generation
- **CONSISTENT**: Web routes use `Route::resource()` with auth middleware
- **CONSISTENT**: API routes use `Route::apiResource()` with auth:sanctum middleware
- **NAMESPACE-AWARE**: Proper controller namespacing in generated routes
- **MIDDLEWARE-READY**: Pre-configured middleware for security

### 🧪 Comprehensive Testing

#### Test Coverage Improvements
- **EXPANDED**: 98 tests with 385+ assertions (94% pass rate)
- **NEW**: Route separation tests ensure web/API routes are properly separated
- **NEW**: Modules loader command tests validate installation process
- **NEW**: API Responser integration tests verify trait functionality
- **ROBUST**: Enhanced test stability and reliability

#### Test Categories
- ✅ Route separation and installation tests
- ✅ API/View mode generation tests
- ✅ Interactive menu functionality tests
- ✅ Command integration tests
- ✅ Stub file validation tests

### 📝 Documentation Updates

#### New Documentation
- **NEW**: `ROUTES_INSTALLATION_DEMO.md` - Complete routes installation guide
- **ENHANCED**: README.md updated with v4.5 features and examples
- **DETAILED**: Step-by-step installation and usage examples
- **TROUBLESHOOTING**: Common issues and solutions guide

#### Updated Examples
- **CURRENT**: All examples updated to reflect v4.5 commands and structure
- **COMPREHENSIVE**: Complete workflow from installation to feature generation
- **VISUAL**: Clear structure diagrams and code examples

### 🐛 Bug Fixes

#### Command Fixes
- **FIXED**: `SetupModulesLoader` now creates both web and API loaders
- **FIXED**: `InstallModulesLoader` integrates both routes properly
- **FIXED**: Route stub inconsistencies resolved
- **FIXED**: Test failures related to route template changes

#### Stability Improvements
- **IMPROVED**: Better error handling in routes installation
- **IMPROVED**: More reliable file system operations
- **IMPROVED**: Enhanced validation of existing installations

---

## [4.4.0] - 2025-07-07

### 🎯 NEW: Generation Mode Options

#### Interactive Menu System
- **NEW**: Interactive menu automatically appears when no mode is specified
- **USER-FRIENDLY**: Clear visual options with descriptions and emoji indicators
- **SMART**: Defaults to Full-stack mode for backwards compatibility
- **FLEXIBLE**: Shows confirmation message after selection

#### Generation Mode Selection
```bash
# Interactive mode (recommended)
php artisan modules:create User
# Shows menu: Full-stack, API Only, View Only

# Direct mode (skip menu)
php artisan modules:create User --api    # API Only
php artisan modules:create User --view   # View Only
```

#### API and View Mode Support
- **NEW**: `--api` option for API-only generation (no Vue views)
- **NEW**: `--view` option for View-only generation (no API routes/requests)
- **ENHANCED**: Full-stack mode remains the default (API + Views)
- **SMART**: Validation prevents using both `--api` and `--view` simultaneously
- **ORGANIZED**: API controllers are placed in `app/Http/Controllers/API/` folder
- **NAMESPACED**: API controllers use `App\Http\Controllers\API` namespace

#### Generation Mode Details

**API-Only Mode (`--api`):**
```bash
php artisan modules:create User --api
```
- ✅ Controller with JSON responses and `auth:sanctum` middleware
- ✅ API routes (`routes/Modules/Users/api.php`)
- ✅ Store/Update Request classes for validation
- ✅ Model, Migration, Permission seeder
- ❌ No Vue views generated

**View-Only Mode (`--view`):**
```bash
php artisan modules:create User --view
```
- ✅ Controller with Inertia responses and `auth` middleware
- ✅ Web routes (`routes/Modules/Users/web.php`)
- ✅ Vue components (Index, Create, Edit, Show)
- ✅ Model, Migration, Permission seeder
- ❌ No Request classes (uses simple validation)

**Full-Stack Mode (default):**
```bash
php artisan modules:create User
```
- ✅ Complete CRUD with both API and Views
- ✅ All components included

#### New Stub Files
- **Added**: `controller.api.stub` for API-only controllers
- **Added**: `controller.view.stub` for View-only controllers
- **Added**: `routes.api.stub` for API-only routes
- **Added**: `routes.view.stub` for View-only routes
- **Enhanced**: Intelligent stub selection based on generation mode

#### Enhanced Test Coverage
- **Added**: 5 new comprehensive tests for API/View options
- **Total**: 74 tests with 286 assertions (100% pass rate)
- **Coverage**: All generation modes thoroughly tested

### 🧪 Updated Test Suite
- **Improved**: All existing tests updated for new command names
- **Added**: `MakeFeatureApiViewOptionsTest` with comprehensive mode testing
- **Validated**: All generation modes work correctly
- **Ensured**: No regressions in existing functionality

## [4.4.0] - 2025-07-07

### 🔄 Command Restructuring

#### Command Renaming
- **BREAKING**: Renamed `make:feature` to `modules:create` for better organization
- **BREAKING**: Renamed `delete:feature` to `modules:delete` for consistency
- **NEW**: Improved command grouping with `features:` namespace for better discoverability

#### Benefits of New Command Structure
- **Consistent Naming**: All feature-related commands now use `features:` prefix
- **Better Organization**: Commands are logically grouped by functionality
- **Improved Discoverability**: `php artisan list features` shows all feature commands
- **Modern Laravel Patterns**: Follows Laravel's command naming conventions

#### Updated Command Usage
```bash
# Old Commands (deprecated)
php artisan make:feature ProductManagement
php artisan delete:feature ProductManagement

# New Commands (v4.4+)
php artisan modules:create ProductManagement
php artisan modules:delete ProductManagement
```

#### Migration Guide
**No code changes required** - only command names have changed:

**Before (v4.3 and earlier):**
```bash
php artisan make:feature User --with=factory,policy,test
php artisan delete:feature User --force
```

**After (v4.4+):**
```bash
php artisan modules:create User --with=factory,policy,test
php artisan modules:delete User --force
```

### 🧪 Updated Test Suite

#### Test Updates
- **Updated**: All unit tests to use new command names
- **Updated**: All feature tests to use new command names  
- **Updated**: All integration tests to use new command names
- **Maintained**: 100% test coverage with 69 tests, 243 assertions

#### Documentation Updates
- **Updated**: README.md with new command examples
- **Updated**: API documentation with correct command signatures
- **Updated**: All code examples and usage instructions

### 🚀 Backward Compatibility Note

**Breaking Change Notice**: This is a breaking change for command-line usage only. Generated code and package functionality remain unchanged. Users need to update their scripts and documentation to use the new command names.

---

## [4.3.0] - 2025-07-06

### 🎨 Major UI/UX Enhancements

#### shadcn-vue Integration
- **BREAKING**: Updated all Vue component stubs to use shadcn-vue components
- **NEW**: Modern, professional UI components with consistent design system
- **NEW**: TypeScript support in all Vue components (`<script setup lang="ts">`)
- **NEW**: Comprehensive component library integration (Button, Card, Table, Badge, etc.)
- **NEW**: Advanced form components with proper validation states
- **NEW**: Responsive grid layouts and mobile-friendly design

### 🐛 Critical Bug Fixes

#### Vue Component Generation
- **FIXED**: Missing placeholder replacements in Vue stub files (`{{ class }}`, `{{ singular }}`)
- **FIXED**: Invalid nested placeholder syntax in pagination (`{{ {{ table }}.data.length }}`)
- **FIXED**: Route generation errors caused by missing placeholder mappings
- **FIXED**: TypeScript interface generation with proper variable names

#### Placeholder Resolution
- **Enhanced**: Added complete placeholder mapping for all Vue components:
  ```php
  'model' => $name,        // Original model name
  'class' => $name,        // PascalCase class name  
  'plural' => $plural,     // Plural form
  'singular' => $singular, // Singular form
  'table' => $table,       // snake_case table name
  'kebab' => Str::kebab($plural), // kebab-case for routes
  'snake' => Str::snake($plural), // snake_case variants
  ```

### ✨ Enhanced Vue Components

#### Index.vue (List View)
- **Professional Data Table** with shadcn-vue Table components
- **Advanced Actions Menu** with DropdownMenu for View/Edit/Delete
- **Status Badges** for created/updated dates
- **Pagination Support** placeholder for future implementation
- **Search and Filter** ready structure
- **Icon Integration** with lucide-vue-next (Plus, Edit, Eye, Trash2, MoreHorizontal)

#### Create.vue (Form Creation)
- **Modern Form Layout** with Card-based design
- **Enhanced Input Components** with Label and error states
- **Loading States** on form submission with disabled buttons
- **Success/Error Feedback** with Alert components
- **Navigation Breadcrumbs** with back button functionality
- **Form Validation** with visual error indicators

#### Edit.vue (Form Editing)
- **Advanced Edit Form** with pre-populated data
- **State Management** with form dirty checking
- **Multiple Action Buttons** (Save, Cancel, View)
- **Success Messages** when form is submitted successfully
- **Enhanced Navigation** between Edit and Show views
- **Optimistic UI Updates** with proper loading states

#### Show.vue (Detail View)
- **Multi-Card Layout** for organized information display
- **Action Buttons** with confirmation dialogs
- **Information Sections** with proper visual hierarchy
- **Quick Actions** with dropdown menu
- **Responsive Design** with grid-based layout
- **Enhanced Typography** with proper spacing and contrast

### 🔧 Technical Improvements

#### TypeScript Integration
- **Full TypeScript Support** in all Vue components
- **Interface Definitions** for props and data structures
- **Type Safety** for better development experience
- **IntelliSense Support** for better code completion

#### Component Architecture
- **AppLayout Integration** for consistent page structure
- **Breadcrumb Navigation** for better user orientation
- **Icon System** with lucide-vue-next integration
- **Theme Support** with dark/light mode compatibility
- **Accessibility** improvements with proper ARIA labels

#### Performance Optimizations
- **Lazy Loading** ready components
- **Optimized Imports** for better bundle size
- **Efficient State Management** with Vue 3 Composition API
- **Memory Efficient** with proper cleanup and disposal

### 📚 Updated Documentation

#### API Documentation Enhancement
- **Comprehensive API Reference** added to README.md
- **Command Documentation** with detailed parameters and examples
- **Usage Examples** for all commands and options
- **Troubleshooting Guide** with common issues and solutions
- **Advanced Configuration** documentation for custom setups

#### Component Documentation
- **Vue Component Structure** documentation
- **shadcn-vue Integration** guide
- **TypeScript Usage** examples and best practices
- **Custom Styling** guidelines for theme customization

### 🧪 Enhanced Testing

#### Test Updates
- **Updated Test Assertions** to support both JavaScript and TypeScript Vue components
- **Enhanced Validation** for shadcn-vue component structure
- **Backward Compatibility** maintained while supporting new features
- **100% Test Coverage** maintained across all components

#### Test Results
- **69 tests** with **243 assertions**
- **100% pass rate** maintained
- **Comprehensive Validation** for all Vue stub files
- **Integration Testing** for generated components

### 🎯 Generated Component Features

#### Modern UI Elements
```typescript
// Professional Table with Actions
<Table>
  <TableHeader>
    <TableRow>
      <TableHead>Name</TableHead>
      <TableHead>Created At</TableHead>
      <TableHead>Updated At</TableHead>
      <TableHead class="w-[100px]">Actions</TableHead>
    </TableRow>
  </TableHeader>
  <TableBody>
    <TableRow v-for="item in items.data" :key="item.id">
      <TableCell class="font-medium">{{ item.name }}</TableCell>
      <TableCell>
        <Badge variant="outline">
          {{ new Date(item.created_at).toLocaleDateString() }}
        </Badge>
      </TableCell>
      <!-- More cells... -->
    </TableRow>
  </TableBody>
</Table>
```

#### Advanced Form Components
```typescript
// Enhanced Form with Validation
<form @submit.prevent="submit" class="space-y-6">
  <div class="space-y-2">
    <Label for="name">Name</Label>
    <Input 
      id="name"
      v-model="form.name" 
      placeholder="Enter name"
      :class="{ 'border-destructive': form.errors.name }"
    />
    <Alert v-if="form.errors.name" variant="destructive">
      <AlertDescription>{{ form.errors.name }}</AlertDescription>
    </Alert>
  </div>
  
  <Button 
    type="submit" 
    :disabled="form.processing"
    class="min-w-[100px]"
  >
    <Save class="mr-2 h-4 w-4" />
    {{ form.processing ? 'Saving...' : 'Save' }}
  </Button>
</form>
```

### 🔄 Migration from v4.2 to v4.3

#### Automatic Migration
```bash
# Update to v4.3 (no breaking changes for existing Laravel apps)
composer require ngodingskuyy/laravel-module-generator:^4.3

# Regenerate components to use new shadcn-vue design (optional)
php artisan modules:delete ExistingFeature --force
php artisan modules:create ExistingFeature --force
```

#### New Dependencies
The generated Vue components now expect these shadcn-vue components to be available:
- `@/components/layouts/AppLayout.vue`
- `@/components/ui/button`
- `@/components/ui/card`
- `@/components/ui/table`
- `@/components/ui/badge`
- `@/components/ui/input`
- `@/components/ui/label`
- `@/components/ui/alert`
- `@/components/ui/dropdown-menu`
- `@/components/ui/separator`
- `@/types` (for BreadcrumbItem interface)
- `lucide-vue-next` (for icons)

### 🎨 Design System Benefits

#### Consistent UI/UX
- **Professional Appearance** with modern design patterns
- **Consistent Spacing** and typography throughout
- **Accessible Components** with proper contrast and ARIA support
- **Responsive Design** that works on all device sizes

#### Developer Experience
- **Type Safety** with full TypeScript support
- **IntelliSense** for better code completion
- **Error Prevention** with compile-time type checking
- **Modern Development** with latest Vue 3 patterns

#### Maintainability
- **Modular Components** easy to customize and extend
- **Clear Code Structure** with separation of concerns
- **Documented APIs** for all generated components
- **Testable Code** with proper component isolation

### 🚀 Performance Improvements

#### Bundle Optimization
- **Tree Shaking** compatible component imports
- **Optimized Bundle Size** with selective component loading
- **Efficient Rendering** with Vue 3 performance optimizations
- **Memory Management** with proper component lifecycle

#### Runtime Performance
- **Fast Initial Load** with optimized component structure
- **Smooth Interactions** with proper state management
- **Efficient Updates** with reactive data binding
- **Minimal Re-renders** with computed properties and watchers

### 🙏 Acknowledgments

Special thanks to:
- **shadcn-vue** team for the excellent component library
- **Vue.js** team for the amazing framework improvements
- **TypeScript** team for enhanced developer experience
- **Lucide** team for the beautiful icon library
- **Community contributors** for feedback and suggestions

---

## [4.2.0] - 2025-07-06

### 🚀 Major Features

#### Feature Deletion System
- **NEW**: Added `modules:delete {name}` command for comprehensive feature removal
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
php artisan modules:delete ProductManagement

# Delete with force (no confirmation)
php artisan modules:delete ProductManagement --force
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
php artisan modules:create ProductManagement

# With optional components (more robust)
php artisan modules:create ProductManagement --with=enum,observer,policy,factory,test
```

### 🔄 Feature Lifecycle Management

The package now supports complete feature lifecycle:

1. **Creation**: `modules:create {name}` - Generate all feature files
2. **Management**: Manual editing and customization
3. **Deletion**: `modules:delete {name}` - Clean removal of all files
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
- **4 main commands**: `modules:create`, `module:delete`, `modules:setup`, `modules:install`
- Complete CRUD feature generation with optional components
- Full feature lifecycle management (create → manage → delete)

### 🎯 Available Commands

#### Core Commands
- `modules:create {name}` - Generate complete CRUD feature
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

The `modules:create {name}` command now generates:

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
