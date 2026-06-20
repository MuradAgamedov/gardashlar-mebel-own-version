<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductDetail\BulkDeleteProductDetailRequest;

use App\Http\Requests\Admin\ProductDetail\ReorderProductDetailRequest;

use App\Http\Requests\Admin\ProductDetail\StoreProductDetailRequest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailTranslate;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\ProductDetailServiceInterface;

class ProductDetailController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected ProductDetailServiceInterface $productDetailService
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product, Request $request)
    {
        $details = $this->productDetailService->getProductDetails($product);
        $currentPage = $request->get('page', 1);
        return view('admin.product-details.index', compact('product', 'details', 'currentPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.product-details.create', compact('product', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductDetailRequest $request, Product $product)
    {
        $request->validated();

        try {
            // === Create product detail for current product ===
            $order = $this->productDetailService->getNextOrder($product->id);
            $productDetail = $this->productDetailService->createDetail($product->id, $order);

            // Create translations for current product detail
            foreach ($request->translates as $translate) {
                ProductDetailTranslate::create([
                    'product_detail_id' => $productDetail->id,
                    'language_id' => $translate['language_id'],
                    'keyword' => $translate['keyword'],
                    'value' => $translate['value'],
                ]);
            }

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Create corresponding product detail with same order
                $correspondingOrder = $this->productDetailService->getNextOrder($sameProduct->id);
                $correspondingDetail = $this->productDetailService->createDetail($sameProduct->id, $correspondingOrder);

                // Create translations with same data
                foreach ($request->translates as $translate) {
                    ProductDetailTranslate::create([
                        'product_detail_id' => $correspondingDetail->id,
                        'language_id' => $translate['language_id'],
                        'keyword' => $translate['keyword'],
                        'value' => $translate['value'],
                    ]);
                }
            }

            return redirect()->route('admin.products.details.index', $product)
                ->with('success', 'Product detail created successfully. Same slug products synchronized.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, ProductDetail $detail)
    {
        $detail->load(['translates.language']);
        return view('admin.product-details.show', compact('product', 'detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductDetail $detail)
    {
        $languages = $this->languageService->getAllLanguages();
        $detail->load(['translates.language']);
        return view('admin.product-details.edit', compact('product', 'detail', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductDetail $detail)
    {
        $request->validated();

        try {
            // === Update current product detail translations ===
            $detail->translates()->delete();

            foreach ($request->translates as $translate) {
                ProductDetailTranslate::create([
                    'product_detail_id' => $detail->id,
                    'language_id' => $translate['language_id'],
                    'keyword' => $translate['keyword'],
                    'value' => $translate['value'],
                ]);
            }

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding product detail by order
                $correspondingDetail = $sameProduct->details()
                    ->where('order', $detail->order)
                    ->first();

                if ($correspondingDetail) {
                    // Delete existing translations for corresponding detail
                    $correspondingDetail->translates()->delete();

                    // Create new translations with same data
                    foreach ($request->translates as $translate) {
                        ProductDetailTranslate::create([
                            'product_detail_id' => $correspondingDetail->id,
                            'language_id' => $translate['language_id'],
                            'keyword' => $translate['keyword'],
                            'value' => $translate['value'],
                        ]);
                    }
                }
            }

            return redirect()->route('admin.products.details.index', $product)
                ->with('success', 'Product detail updated successfully. Same slug products synchronized.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetail $detail)
    {
        try {
            // Store the order for synchronization
            $detailOrder = $detail->order;

            // === Delete current product detail ===
            $detail->translates()->delete();
            $detail->delete();

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding product detail by order
                $correspondingDetail = $sameProduct->details()
                    ->where('order', $detailOrder)
                    ->first();

                if ($correspondingDetail) {
                    // Delete corresponding detail and its translations
                    $correspondingDetail->translates()->delete();
                    $correspondingDetail->delete();
                }
            }

            return redirect()->route('admin.products.details.index', $product)
                ->with('success', 'Product detail deleted successfully. Same slug products synchronized.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reorder product details
     */
    public function reorder(ReorderProductDetailRequest $request, Product $product)
    {
        $request->validated();

        foreach ($request->items as $index => $id) {
            $this->productDetailService->updateOrder($id, $index + 1);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete product details
     */
    public function bulkDelete(BulkDeleteProductDetailRequest $request, Product $product)
    {
        $request->validated();

        try {
            $details = $this->productDetailService->getDetailsByIds($request->ids);
            
            // Store orders for synchronization
            $detailOrders = $details->pluck('order')->toArray();

            // === Delete current product details ===
            foreach ($details as $detail) {
                $detail->translates()->delete();
                $detail->delete();
            }

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding product details by orders
                $correspondingDetails = $sameProduct->details()
                    ->whereIn('order', $detailOrders)
                    ->get();

                foreach ($correspondingDetails as $correspondingDetail) {
                    // Delete corresponding detail and its translations
                    $correspondingDetail->translates()->delete();
                    $correspondingDetail->delete();
                }
            }

            return response()->json(['success' => true, 'message' => 'Product details deleted successfully. Same slug products synchronized.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error occurred: ' . $e->getMessage()]);
        }
    }
}
