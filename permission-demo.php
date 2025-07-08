<?php

/**
 * Demo Implementation: Spatie Laravel Permission Integration
 * 
 * This script demonstrates the permission middleware implementation
 * that has been added to all controller stubs.
 */

echo "🛡️  SPATIE LARAVEL PERMISSION IMPLEMENTATION DEMO\n";
echo str_repeat("=", 60) . "\n\n";

echo "📋 IMPLEMENTED FEATURES:\n\n";

echo "1. ✅ API Controller (controller.api.stub)\n";
echo "   - Permission middleware for: view, create, update, delete\n";
echo "   - Method-specific constraints\n";
echo "   - Uses auth:sanctum middleware\n\n";

echo "2. ✅ Web Controller (controller.stub)\n";
echo "   - Permission middleware for: view, create, update, delete\n";
echo "   - Method-specific constraints\n";
echo "   - Uses auth middleware\n\n";

echo "3. ✅ View Controller (controller.view.stub)\n";
echo "   - Permission middleware for: view, create, update, delete\n";
echo "   - Method-specific constraints\n";
echo "   - Uses auth middleware\n\n";

echo "4. ✅ Permission Seeder (seeder.permission.stub)\n";
echo "   - Auto-generates permissions: view, create, update, delete, restore, force_delete\n";
echo "   - Uses dynamic permission names based on module\n\n";

echo "📄 CONTROLLER IMPLEMENTATION EXAMPLE:\n";
echo str_repeat("-", 60) . "\n";

$exampleController = <<<'PHP'
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

    public function index(Request $request): JsonResponse
    {
        // Only users with 'view products' permission can access
        $query = Product::query();
        // ... rest of method
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        // Only users with 'create products' permission can access
        $product = Product::create($request->validated());
        // ... rest of method
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        // Only users with 'update products' permission can access
        $product->update($request->validated());
        // ... rest of method
    }

    public function destroy(Product $product): JsonResponse
    {
        // Only users with 'delete products' permission can access
        $product->delete();
        // ... rest of method
    }
}
PHP;

echo $exampleController . "\n\n";

echo "📄 PERMISSION SEEDER EXAMPLE:\n";
echo str_repeat("-", 60) . "\n";

$exampleSeeder = <<<'PHP'
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
PHP;

echo $exampleSeeder . "\n\n";

echo "🚀 USAGE COMMANDS:\n";
echo str_repeat("-", 60) . "\n";
echo "# Generate API-only module with permissions\n";
echo "php artisan module:create Product --api\n\n";

echo "# Generate full-stack module with permissions\n"; 
echo "php artisan module:create Category\n\n";

echo "# Generate view-only module with permissions\n";
echo "php artisan module:create Order --view\n\n";

echo "💡 PERMISSION NAMES GENERATED:\n";
echo str_repeat("-", 60) . "\n";
echo "For module 'Product', permissions created:\n";
echo "- view products\n";
echo "- create products\n";
echo "- update products\n";
echo "- delete products\n";
echo "- restore products\n";
echo "- force_delete products\n\n";

echo "🔧 NEXT STEPS:\n";
echo str_repeat("-", 60) . "\n";
echo "1. Run the permission seeder: php artisan db:seed --class=ProductsPermissionSeeder\n";
echo "2. Assign permissions to roles using Spatie Laravel Permission\n";
echo "3. Assign roles to users\n";
echo "4. Controllers will automatically check permissions!\n\n";

echo "✅ Implementation completed successfully!\n";
echo "🛡️  All controllers now use Spatie Laravel Permission middleware.\n";
