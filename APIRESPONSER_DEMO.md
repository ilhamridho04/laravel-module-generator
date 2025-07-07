# Demo: Laravel Module Generator dengan ApiResponser Trait

## Features yang telah diimplementasi:

### ✅ ApiResponser Trait 
- **Lokasi**: `src/stubs/api-responser.trait.stub`
- **Fitur**: Trait reusable untuk konsistensi response JSON API
- **Methods**: 
  - `successResponse()` - Response sukses
  - `errorResponse()` - Response error
  - `validationErrorResponse()` - Response error validasi
  - `notFoundResponse()` - Response not found 
  - `unauthorizedResponse()` - Response unauthorized
  - `forbiddenResponse()` - Response forbidden

### ✅ API Controller Stub yang telah diperbarui
- **Lokasi**: `src/stubs/controller.api.stub`
- **Namespace**: `App\Http\Controllers\API`
- **Uses**: `ApiResponser` trait untuk konsistensi response
- **Fitur**: 
  - Semua method menggunakan `$this->successResponse()`
  - Error handling yang konsisten
  - Message yang informatif untuk setiap action

### ✅ Logic pengecekan otomatis
- **Method**: `ensureApiResponserTrait()` dalam `MakeFeature.php`
- **Fitur**: 
  - Cek jika trait sudah ada
  - Buat folder `app/Traits` jika belum ada
  - Copy trait dari stub jika belum ada
  - Otomatis dipanggil saat mode API atau Full-stack

### ✅ Test Coverage
- **Feature Tests**: `MakeFeatureApiResponserTest.php` 
  - Test stub files sudah benar
  - Test API controller menggunakan trait
  - Test namespace yang benar
- **Unit Tests**: `MakeFeatureApiResponserUnitTest.php`
  - Test method `determineGenerationMode()`
  - Test stub generation logic
  - Test API routes stub

## Contoh Generated API Controller:

```php
<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use AuthorizesRequests, ApiResponser;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(10)->withQueryString();

        return $this->successResponse([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ], 'Product list retrieved successfully');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        
        return $this->successResponse($product, 'Product created successfully', 201);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse($product, 'Product retrieved successfully');
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        
        return $this->successResponse($product, 'Product updated successfully');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        
        return $this->successResponse(null, 'Product deleted successfully');
    }
}
```

## Contoh Generated ApiResponser Trait:

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function validationErrorResponse($errors, string $message = 'Validation error', int $code = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
}
```

## Konsistensi Response JSON:

### Success Response:
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Sample Product",
    "created_at": "2025-01-07T12:00:00.000000Z",
    "updated_at": "2025-01-07T12:00:00.000000Z"
  }
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

## Test Results:
```
Tests: 91, Assertions: 354
✅ ApiResponser trait tests: PASSED
✅ Stub file tests: PASSED  
✅ Unit tests: PASSED
✅ API/View options tests: PASSED
```

## Command Usage:

```bash
# Generate API-only feature with ApiResponser trait
php artisan features:create Product --api

# Generate full-stack feature with ApiResponser trait  
php artisan features:create Product

# Generate view-only feature (no ApiResponser trait)
php artisan features:create Product --view
```

Trait akan otomatis dibuat di `app/Traits/ApiResponser.php` dan digunakan oleh semua API controllers yang dihasilkan.
