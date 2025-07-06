# Laravel Module Generator

A modular Laravel + Vue 3 + TailwindCSS + shadcn-vue CRUD feature generator.

Created by **NgodingSkuyy** to accelerate development using standardized full-stack architecture.

---

## 🚀 Features

- ✅ Generate full CRUD: model, migration, controller, request, route, seeder, and Vue pages
- 📦 Modular structure for better separation per feature
- 🧱 TailwindCSS + shadcn-vue powered Vue 3 frontend
- 🔐 Permissions seeded automatically using Spatie Laravel Permission
- 🧰 Stub-based: fully customizable with `php artisan vendor:publish`

---

## 📦 Installation

```bash
composer require ngodingskuyy/laravel-module-generator --dev
```

If you're testing it locally:

```json
"repositories": [
  {
    "type": "path",
    "url": "./packages/NcodingSkuyy/LaravelModuleGenerator"
  }
]
```

Then:

```bash
composer require ngodingskuyy/laravel-module-generator:@dev
```

---

## 🔧 Usage

```bash
php artisan make:feature User
```

Will generate:

- `app/Models/User.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Requests/StoreUserRequest.php`, `UpdateUserRequest.php`
- `resources/js/pages/Users/Index.vue`, `Create.vue`, `Edit.vue`, `Show.vue`
- `routes/Modules/Users/web.php`
- `database/seeders/Permission/UsersPermissionSeeder.php`

---

## 🧩 Customizing Stubs

Publish stub files to customize:

```bash
php artisan vendor:publish --tag=laravel-module-generator-stubs
```

This will copy stubs to:
```
stubs/laravel-module-generator/
```

Modify anything to fit your coding style (e.g. validation, layout, structure, etc).

---

## 📄 License

MIT © 2025 [NgodingSkuyy](https://github.com/ngodingskuyy)
