<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductDetailValue;
use App\Models\ProductDetailValueTranslate;

class ProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $product->load(['details.translates.language']);
        $languages = Language::active()->orderBy('order')->get();
        
        return view('admin.product-details.index', compact('product', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.product-details.create', compact('product', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'translates' => 'required|array|min:1',
            'translates.*.keyword' => 'required|string|max:255',
            'translates.*.value' => 'required|string|max:1000',
            'translates.*.valuekeyword' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Group translations by language
            $translationsByLanguage = [];
            foreach ($request->translates as $index => $translate) {
                $languageId = $request->input("translates.{$index}.language_id", 1); // Default to first language
                if (!isset($translationsByLanguage[$languageId])) {
                    $translationsByLanguage[$languageId] = [];
                }
                $translationsByLanguage[$languageId][] = $translate;
            }

            // Process each language
            foreach ($translationsByLanguage as $languageId => $translations) {
                foreach ($translations as $translation) {
                    // Create product detail
                    $productDetail = ProductDetail::create([
                        'product_id' => $product->id,
                        'order' => ProductDetail::where('product_id', $product->id)->max('order') + 1
                    ]);

                    // Create product detail translate
                    ProductDetailTranslate::create([
                        'product_detail_id' => $productDetail->id,
                        'language_id' => $languageId,
                        'keyword' => $translation['keyword'],
                        'value' => $translation['value']
                    ]);

                    // If valuekeyword exists, create product detail value
                    if (isset($translation['valuekeyword']) && !empty($translation['valuekeyword'])) {
                        $productDetailValue = ProductDetailValue::create([
                            'product_detail_id' => $productDetail->id,
                            'order' => ProductDetailValue::where('product_detail_id', $productDetail->id)->max('order') + 1
                        ]);

                        // Create product detail value translate
                        ProductDetailValueTranslate::create([
                            'product_detail_value_id' => $productDetailValue->id,
                            'language_id' => $languageId,
                            'keyword' => $translation['valuekeyword'],
                            'value' => $translation['value']
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.products.details.index', $product)
                ->with('success', 'Product detail created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating product detail: ' . $e->getMessage())
                ->withInput();
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
        $detail->load(['translates.language']);
        $languages = Language::active()->orderBy('order')->get();
        
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
            'translates.*.value' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing translations
            $detail->translates()->delete();

            // Create new translations
            foreach ($request->translates as $translate) {
                ProductDetailTranslate::create([
                    'product_detail_id' => $detail->id,
                    'language_id' => $translate['language_id'],
                    'keyword' => $translate['keyword'],
                    'value' => $translate['value']
                ]);
            }

            DB::commit();

            return redirect()->route('admin.products.details.index', $product)
                ->with('success', 'Product detail updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating product detail: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetail $detail)
    {
        try {
            $detail->delete();
            return redirect()->route('admin.products.details.index', $product)
                ->with('success', 'Product detail deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting product detail: ' . $e->getMessage());
        }
    }

    /**
     * Reorder product details
     */
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:product_details,id'
        ]);

        try {
            foreach ($request->items as $index => $id) {
                ProductDetail::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete selected details
     */
    public function bulkDelete(Request $request, Product $product)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:product_details,id'
        ]);

        try {
            ProductDetail::whereIn('id', $request->ids)->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
