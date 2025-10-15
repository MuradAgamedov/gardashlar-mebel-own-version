<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use App\Repositories\ProductDetailRepository;

class ProductDetailController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public ProductDetailRepository $repository
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $details = $this->repository->getProductDetails($product);
        return view('admin.product-details.index', compact('product', 'details'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.product-details.create', compact('product', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.keyword' => 'required|string|max:255',
            'translates.*.value' => 'required|string',
        ]);

        try {
            // === Create product detail for current product ===
            $order = $this->repository->getNextOrder($product->id);
            $productDetail = $this->repository->createDetail($product->id, $order);

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
                $correspondingOrder = $this->repository->getNextOrder($sameProduct->id);
                $correspondingDetail = $this->repository->createDetail($sameProduct->id, $correspondingOrder);

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
        $languages = $this->languageRepository->getAllLanguages();
        $detail->load(['translates.language']);
        return view('admin.product-details.edit', compact('product', 'detail', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductDetail $detail)
    {
        $request->validate([
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.keyword' => 'required|string|max:255',
            'translates.*.value' => 'required|string',
        ]);

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
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:product_details,id'
        ]);

        foreach ($request->items as $index => $id) {
            $this->repository->updateOrder($id, $index + 1);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete product details
     */
    public function bulkDelete(Request $request, Product $product)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_details,id'
        ]);

        try {
            $details = $this->repository->getDetailsByIds($request->ids);
            
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
