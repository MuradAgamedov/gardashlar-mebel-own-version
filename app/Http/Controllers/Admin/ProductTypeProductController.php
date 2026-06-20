<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomePageTypeProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Services\Admin\ProductTypeProductServiceInterface;

class ProductTypeProductController extends Controller
{
    public function __construct(protected ProductTypeProductServiceInterface $productTypeProductService)
    {
    }
    /**
     * Display products for a specific product type
     */
    public function index(Request $request, $productTypeId)
    {
        $productType = $this->productTypeProductService->getProductType($productTypeId);
        $products = $this->productTypeProductService->getProductsByType($productTypeId, $request);
        $homePageProductIds = $this->productTypeProductService->getHomePageProductIds($productTypeId);

        return view('admin.product-type-products.index', compact('products', 'productType', 'homePageProductIds'));
    }

    /**
     * Toggle product visibility on homepage for this type
     */
    public function toggleHomePage(Request $request, $productTypeId, $productId)
    {
        try {
            $existing = $this->productTypeProductService->checkHomePageStatus($productTypeId, $productId);

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
        
        $products = $this->productTypeProductService->searchProductsByType($productTypeId, $search);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
