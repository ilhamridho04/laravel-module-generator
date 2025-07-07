# 🚀 Routes Installation Demo

## 📋 **Cara Install Routes Auto-loader**

### 1. **Setup Otomatis (Recommended)**
```bash
# Setup loader files
php artisan modules:setup

# Install dan integrasikan ke routes
php artisan modules:install
```

### 2. **Setup Manual**
```bash
# Hanya buat file loader
php artisan modules:setup

# Kemudian manual tambahkan ke routes/web.php:
require __DIR__ . '/modules.php';

# Dan ke routes/api.php:
require __DIR__ . '/api-modules.php';
```

### 3. **Auto-install saat Generate Feature**
```bash
# Saat generate feature, sistem akan otomatis tanya:
php artisan modules:create Product

# Output:
# ⚠️  Untuk mengaktifkan auto-loading web modules, pilih salah satu:
#    1. Otomatis install:
#       php artisan modules:install
#
#    2. Manual install:
#       Di routes/web.php:
#       require __DIR__ . '/modules.php';
#
# 🤔 Mau auto-install sekarang? (yes/no) [yes]:
```

## 📁 **Struktur yang Dihasilkan**

### **Setelah `modules:setup`**
```
routes/
├── modules.php        # Web modules loader
├── api-modules.php    # API modules loader
└── Modules/           # Feature modules folder
```

### **Setelah `modules:install`**
```
routes/
├── web.php            # Updated dengan require modules.php
├── api.php            # Updated dengan require api-modules.php
├── modules.php        # Web modules loader
├── api-modules.php    # API modules loader
└── Modules/           # Feature modules folder
```

### **Setelah `modules:create Product`**
```
routes/
├── web.php            # With modules.php included
├── api.php            # With api-modules.php included  
├── modules.php        # Web modules loader
├── api-modules.php    # API modules loader
└── Modules/
    └── Products/
        ├── web.php    # Web routes
        └── api.php    # API routes
```

## 🎯 **Fitur Commands**

### **modules:setup**
- Membuat `routes/modules.php` (web loader)
- Membuat `routes/api-modules.php` (API loader)
- Memberikan instruksi instalasi

### **modules:install**
- Menjalankan `modules:setup` jika belum ada
- Auto-integrate ke `routes/web.php` atau `routes/app.php`
- Auto-integrate ke `routes/api.php`
- Backup otomatis jika ada konflik

### **modules:create** 
- Deteksi loader sudah terpasang atau belum
- Offer auto-install jika belum terpasang
- Generate routes terpisah (web.php dan api.php)

## ✅ **Keunggulan Sistem Baru**

1. **Terpisah dengan Jelas**: Web dan API routes tidak tercampur
2. **Auto-install**: Tidak perlu setup manual
3. **Smart Detection**: Deteksi otomatis apakah sudah terpasang
4. **Laravel 11+ Support**: Support `routes/app.php` dan `routes/web.php`
5. **Backup Safe**: Tidak akan overwrite tanpa konfirmasi

## 🔧 **Troubleshooting**

### **Jika routes tidak terbaca:**
```bash
# Pastikan loader sudah terpasang
php artisan modules:install --force

# Atau cek manual di routes/web.php dan routes/api.php
# Pastikan ada: require __DIR__ . '/modules.php';
# Dan: require __DIR__ . '/api-modules.php';
```

### **Jika ingin reset:**
```bash
# Hapus file loader
rm routes/modules.php
rm routes/api-modules.php

# Setup ulang
php artisan modules:setup --force
php artisan modules:install --force
```
