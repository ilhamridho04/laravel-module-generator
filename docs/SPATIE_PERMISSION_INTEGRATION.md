# Spatie Laravel Permission Integration

## 📋 Overview

Implementasi ini mengintegrasikan **Spatie Laravel Permission** ke dalam semua controller yang dihasilkan oleh Laravel Module Generator. Setiap controller akan secara otomatis menggunakan middleware permission untuk mengontrol akses berdasarkan permissions yang telah didefinisikan.

## 🛡️ Features Implemented

### 1. **Permission Middleware pada Controllers**

Semua controller stubs telah diupdate untuk menggunakan permission middleware:

- **API Controller** (`controller.api.stub`)
- **Web Controller** (`controller.stub`) 
- **View Controller** (`controller.view.stub`)

### 2. **Method-Specific Permission Control**

Setiap controller menggunakan permission yang spesifik untuk setiap method:

- `view {module}` - untuk method `index` dan `show`
- `create {module}` - untuk method `create` dan `store`
- `update {module}` - untuk method `edit` dan `update`  
- `delete {module}` - untuk method `destroy`

### 3. **Automatic Permission Seeder**

Setiap module yang dibuat akan menghasilkan Permission Seeder yang otomatis membuat permissions:
- `view {module}`
- `create {module}`
- `update {module}`
- `delete {module}`
- `restore {module}`
- `force_delete {module}`

## 🚀 Usage

### Generate Module dengan Permission

```bash
# API-only module
php artisan module:create Product --api

# Full-stack module  
php artisan module:create Category

# View-only module
php artisan module:create Order --view
```

### Setup Permissions

1. **Run Permission Seeder:**
```bash
php artisan db:seed --class=ProductsPermissionSeeder
```

2. **Assign Permissions to Roles:**
```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create role
$adminRole = Role::create(['name' => 'admin']);

// Assign permissions
$adminRole->givePermissionTo([
    'view products',
    'create products', 
    'update products',
    'delete products'
]);
```

3. **Assign Role to User:**
```php
$user = User::find(1);
$user->assignRole('admin');
```

## 📄 Generated Code Examples

### API Controller Example

```php
<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only(['store']);
        $this->middleware('permission:update products')->only(['update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    // Methods with automatic permission checking...
}
```

### Permission Seeder Example

```php
<?php

namespace Database\Seeders\Permission;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ProductsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['view', 'create', 'update', 'delete', 'restore', 'force_delete'];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => "$perm products"]);
        }
    }
}
```

## 🔧 Permission Naming Convention

Untuk module dengan nama `Product`, permissions yang dibuat:

- `view products` - Melihat daftar dan detail produk
- `create products` - Membuat produk baru
- `update products` - Mengupdate produk yang ada
- `delete products` - Menghapus produk
- `restore products` - Mengembalikan produk yang dihapus (soft delete)
- `force_delete products` - Menghapus permanent produk

## 🧪 Testing

Implementasi ini telah ditest dengan comprehensive test suite:

```bash
vendor/bin/phpunit tests/Feature/MakeFeaturePermissionTest.php
```

Test coverage meliputi:
- ✅ API Controller permission middleware
- ✅ Web Controller permission middleware  
- ✅ Full-stack Controller permission middleware
- ✅ Permission Seeder generation

## 💡 Benefits

1. **Automatic Security**: Setiap controller otomatis dilindungi permission middleware
2. **Consistent Permission Names**: Naming convention yang konsisten untuk semua module
3. **Easy Role Management**: Integration yang mudah dengan Spatie Laravel Permission
4. **Granular Control**: Permission yang spesifik per method/action
5. **Auto-Generated Seeders**: Tidak perlu membuat permission seeder manual

## 🔗 Dependencies

Pastikan Spatie Laravel Permission sudah terinstall:

```bash
composer require spatie/laravel-permission
```

Dan sudah dikonfigurasi sesuai [dokumentasi Spatie](https://spatie.be/docs/laravel-permission/v6/introduction).

## ✅ Status

**🎉 IMPLEMENTATION COMPLETED!**

Semua controller stubs telah diupdate dan siap digunakan dengan Spatie Laravel Permission middleware.
