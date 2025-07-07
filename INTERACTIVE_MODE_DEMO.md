# Fully Interactive Mode Demo

## Overview
The `modules:create` command now supports a fully interactive mode when no feature name is provided.

## Usage

### Interactive Mode (No Arguments)
```bash
php artisan modules:create
```

When you run this command without any arguments, it will:

1. **Prompt for Feature Name**
   ```
   📝 Masukkan nama fitur (contoh: Product, UserProfile, Category):
   ```

2. **Show Preview and Confirmation**
   ```
   ✨ Preview fitur yang akan dibuat:
      📂 Model: Product
      📂 Table: products
      📂 Routes: /products
      📂 Views: resources/js/pages/Products/
   
   ✅ Lanjutkan dengan nama ini? (yes/no) [yes]:
   ```

3. **Mode Selection Menu**
   ```
   🎯 Pilih mode pembuatan fitur:
      1. Full-stack (API + Views) - Lengkap dengan controller, routes, views
      2. API Only - Hanya API controller, routes, dan requests
      3. View Only - Hanya Vue views dan web controller
   
   🤔 Pilih mode generation:
     [1] Full-stack (API + Views)
     [2] API Only
     [3] View Only
   ```

4. **Optional Components Selection**
   ```
   🔧 Pilih komponen tambahan (opsional):

   📦 Pilih komponen (ketik nomor, pisahkan dengan koma untuk multiple, contoh: 1,3,5):
      [0] Tidak ada komponen tambahan
      [1] Enum - Status enum untuk model
      [2] Observer - Model observer untuk event handling
      [3] Policy - Authorization policy
      [4] Factory - Model factory untuk testing/seeding
      [5] Test - Feature test untuk CRUD operations

   🎯 Masukkan pilihan Anda (default: 0): 1,3,4
   ```

## Command Variations

### With Name (Traditional)
```bash
php artisan modules:create Product
# Still shows mode selection and optional components if not specified via flags
```

### With Flags (Skip Interactive Menus)
```bash
# API only mode
php artisan modules:create Product --api

# View only mode  
php artisan modules:create Product --view

# With optional components
php artisan modules:create Product --with=enum,observer,policy,factory,test

# Force overwrite
php artisan modules:create Product --force

# Skip auto-install prompt (for tests)
php artisan modules:create Product --skip-install
```

## Interactive Workflow Examples

### Full Example - Interactive Mode
```bash
php artisan modules:create

# User input flow:
# 1. Enter "Product" as feature name
# 2. Confirm "yes" to proceed
# 3. Select "1" for Full-stack mode
# 4. Enter "1,3" to select Enum and Policy

# Result: Creates full-stack Product feature with Enum and Policy
```

### Mixed Mode - Name Provided, Interactive Options
```bash
php artisan modules:create Product

# Since name is provided, skips name prompt but still shows:
# 1. Mode selection menu (if no --api or --view flag)
# 2. Optional components prompt (if no --with flag)
```

### Full Non-Interactive Mode
```bash
php artisan modules:create Product --api --with=enum,policy --force

# Completely non-interactive:
# - Uses provided name "Product"
# - Uses API-only mode
# - Includes enum and policy components
# - Forces overwrite of existing files
```

## Multi-Select Examples

### No Optional Components
```
🎯 Masukkan pilihan Anda (default: 0): 0
# or just press Enter for default
🎯 Masukkan pilihan Anda (default: 0): 
```

### Single Component
```
🎯 Masukkan pilihan Anda (default: 0): 1
# Selects only Enum
```

### Multiple Components
```
🎯 Masukkan pilihan Anda (default: 0): 1,3,5
# Selects Enum, Policy, and Test

🎯 Masukkan pilihan Anda (default: 0): 2,4
# Selects Observer and Factory
```

### All Components
```
🎯 Masukkan pilihan Anda (default: 0): 1,2,3,4,5
# Selects all available components
```

## Key Features

- **Smart Validation**: Validates feature names for proper format
- **Preview Generation**: Shows exactly what will be created before proceeding
- **Flexible Mode Selection**: Choose between Full-stack, API-only, or View-only
- **Optional Components**: Pick and choose additional components as needed
- **Backward Compatibility**: All existing flag-based commands still work
- **Test-Friendly**: Supports `--skip-install` for automated testing

The interactive mode makes the package much more user-friendly, especially for developers new to the package or when exploring different feature configurations.
