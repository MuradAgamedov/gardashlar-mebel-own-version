<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomePageTypeProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductTypeProductController extends Controller
{
    /**
     * Display products for a specific product type
     */
    public function index(Request $request, $productTypeId)
    {
        $productType = ProductType::findOrFail($productTypeId);
        
        $query = Product::whereHas('types', function($q) use ($productTypeId) {
            $q->where('product_types.id', $productTypeId);
        })->with(['types', 'categories', 'translates']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('order')->paginate(20);

        // Get products that are already on homepage for this type
        $homePageProductIds = HomePageTypeProduct::where('product_type_id', $productTypeId)
            ->pluck('product_id')
            ->toArray();

        return view('admin.product-type-products.index', compact('products', 'productType', 'homePageProductIds'));
    }

    /**
     * Toggle product visibility on homepage for this type
     */
    public function toggleHomePage(Request $request, $productTypeId, $productId)
    {
        try {
            $existing = HomePageTypeProduct::where('product_type_id', $productTypeId)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                // Remove from homepage
                $existing->delete();
                $message = 'Product removed from homepage';
                $isOnHomepage = false;
            } else {
                // Add to homepage
                HomePageTypeProduct::create([
                    'product_type_id' => $productTypeId,
                    'product_id' => $productId,
                ]);
                $message = 'Product added to homepage';
                $isOnHomepage = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'isOnHomepage' => $isOnHomepage
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products for this type (AJAX)
     */
    public function search(Request $request, $productTypeId)
    {
        $search = $request->search;
        
        $products = Product::whereHas('types', function($q) use ($productTypeId) {
            $q->where('product_types.id', $productTypeId);
        })
        ->whereHas('translates', function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        })
        ->with(['translates', 'types', 'categories'])
        ->orderBy('order')
        ->limit(10)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
