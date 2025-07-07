# Laravel Module Generator - Interactive Delete Demo

## Features Delete Interactive Mode

Laravel Module Generator sekarang mendukung interactive mode untuk deletion dengan 3 pilihan:

### 1. Full Deletion (API + Views)
Menghapus semua komponen:
- Model
- API Controller (`app/Http/Controllers/API/`)
- Web Controller (`app/Http/Controllers/`)
- Request classes
- Vue Views
- API Routes
- Web Routes
- Permission seeder
- Migration files

### 2. API Only
Menghapus hanya komponen API:
- API Controller (`app/Http/Controllers/API/`)
- API Routes (`routes/Modules/{Feature}/api.php`)
- Request classes
- ApiResponser trait (jika tidak ada API controller lain)

### 3. View Only  
Menghapus hanya komponen View:
- Web Controller (`app/Http/Controllers/`)
- Web Routes (`routes/Modules/{Feature}/web.php`)
- Vue Views (`resources/js/pages/{Feature}/`)

## Usage Examples

### Interactive Mode (Default)
```bash
php artisan features:delete ProductName
```
Akan menampilkan menu:
```
🎯 Pilih mode penghapusan fitur:
   1. Full Deletion (API + Views) - Hapus semua controller, routes, views
   2. API Only - Hapus hanya API controller dan routes  
   3. View Only - Hapus hanya Vue views dan web controller

🤔 Pilih mode deletion [Full Deletion (API + Views)]:
  [1] Full Deletion (API + Views)
  [2] API Only
  [3] View Only
 >
```

### Command Line Flags
```bash
# API Only
php artisan features:delete ProductName --api --force

# View Only  
php artisan features:delete ProductName --view --force

# Full Deletion (tanpa konfirmasi)
php artisan features:delete ProductName --force
```

### Error Validation
```bash
# Error - tidak bisa keduanya
php artisan features:delete ProductName --api --view
# Output: ❌ Tidak bisa menggunakan --api dan --view bersamaan. Pilih salah satu atau kosongkan untuk full deletion.
```

## Command Signature
```bash
features:delete {name} 
            {--with=* : Optional components to delete like enum, observer, policy, factory, test} 
            {--all : Delete all related files including optional components} 
            {--force : Delete without confirmation}
            {--api : Delete API-only components}
            {--view : Delete View-only components}
```

## Demo Output

### Creating Full-Stack Feature
```bash
$ php artisan features:create DemoProduct --skip-install
🎯 Pilih mode pembuatan fitur:
   1. Full-stack (API + Views) - Lengkap dengan controller, routes, views
   2. API Only - Hanya API controller, routes, dan requests
   3. View Only - Hanya Vue views dan web controller

🤔 Pilih mode generation [Full-stack (API + Views)]:
 > 1

✅ Mode Full-stack dipilih
🔧 Membuat fitur: DemoProducts (demo-products) - Mode: Full-stack (API + View)
🎮 API Controller dibuat: API/DemoProductController.php
🎮 Web Controller dibuat: DemoProductController.php
🛣️ API route file dibuat: routes/Modules/DemoProducts/api.php
🛣️ Web route file dibuat: routes/Modules/DemoProducts/web.php
✅ Fitur DemoProducts berhasil dibuat!
```

### Deleting API Only
```bash
$ php artisan features:delete DemoProduct --api --force
🗑️ Menghapus fitur: DemoProducts (demo-products) - Mode: API Only

📋 File yang akan dihapus:
  🗑️  app\Http/Controllers/API/DemoProductController.php
  🗑️  routes/Modules/DemoProducts/api.php
  🗑️  app\Http/Requests/StoreDemoProductRequest.php
  🗑️  app\Http/Requests/UpdateDemoProductRequest.php

✅ Dihapus: app\Http/Controllers/API/DemoProductController.php
✅ Dihapus: routes/Modules/DemoProducts/api.php
✅ Dihapus: app\Http/Requests/StoreDemoProductRequest.php
✅ Dihapus: app\Http/Requests/UpdateDemoProductRequest.php
✅ Fitur DemoProducts berhasil dihapus! (4 file dihapus)
```

### Deleting View Only
```bash
$ php artisan features:delete DemoProduct --view --force
🗑️ Menghapus fitur: DemoProducts (demo-products) - Mode: View Only

📋 File yang akan dihapus:
  🗑️  app\Http/Controllers/DemoProductController.php
  🗑️  routes/Modules/DemoProducts/web.php
  🗑️  resources\js/pages/DemoProducts/Index.vue
  🗑️  resources\js/pages/DemoProducts/Create.vue
  🗑️  resources\js/pages/DemoProducts/Edit.vue
  🗑️  resources\js/pages/DemoProducts/Show.vue

✅ Dihapus: app\Http/Controllers/DemoProductController.php
✅ Dihapus: routes/Modules/DemoProducts/web.php
✅ Dihapus: resources\js/pages/DemoProducts/Index.vue
✅ Dihapus: resources\js/pages/DemoProducts/Create.vue
✅ Dihapus: resources\js/pages/DemoProducts/Edit.vue
✅ Dihapus: resources\js/pages/DemoProducts/Show.vue
🗂️  Direktori kosong dihapus: resources\js/pages/DemoProducts
✅ Fitur DemoProducts berhasil dihapus! (6 file dihapus)
```

## Smart Cleanup Features

1. **Automatic Directory Cleanup**: Menghapus direktori kosong setelah deletion
2. **ApiResponser Trait Protection**: Hanya menghapus `ApiResponser.php` jika tidak ada API controller lain
3. **Selective Deletion**: Mode API/View only hanya menghapus komponen yang relevan
4. **Migration Protection**: Migration hanya dihapus pada full deletion mode

## Summary

Interactive delete mode memberikan fleksibilitas untuk:
- 🔥 **Full Deletion**: Bersihkan semua komponen fitur
- 🌐 **API Only**: Hapus hanya backend API, pertahankan frontend
- 🖼️ **View Only**: Hapus hanya frontend views, pertahankan API

Perfect untuk development workflow yang membutuhkan partial cleanup atau refactoring!
