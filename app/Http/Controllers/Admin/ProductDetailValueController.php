<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetailValue;
use App\Models\ProductDetailValueTranslate;
use App\Models\Language;
use Illuminate\Http\Request;

class ProductDetailValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $detailValues = ProductDetailValue::with(['translates.language'])->ordered()->get();
        return view('admin.product-detail-values.index', compact('product', 'detailValues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $languages = Language::all();
        return view('admin.product-detail-values.create', compact('product', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.value' => 'required|string',
        ]);

        // Create product detail value
        $productDetailValue = ProductDetailValue::create([
            'keyword' => $request->keyword,
            'order' => ProductDetailValue::max('order') + 1
        ]);

        // Create translations
        foreach ($request->translates as $translate) {
            ProductDetailValueTranslate::create([
                'product_detail_value_id' => $productDetailValue->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
            ]);
        }

        return redirect()->route('admin.products.detail-values.index', $product)
            ->with('success', 'Product detail value created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductDetailValue $detailValue)
    {
        $languages = Language::all();
        $detailValue->load(['translates.language']);
        return view('admin.product-detail-values.edit', compact('product', 'detailValue', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductDetailValue $detailValue)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.value' => 'required|string',
        ]);

        // Update keyword
        $detailValue->update([
            'keyword' => $request->keyword
        ]);

        // Delete existing translations
        $detailValue->translates()->delete();

        // Create new translations
        foreach ($request->translates as $translate) {
            ProductDetailValueTranslate::create([
                'product_detail_value_id' => $detailValue->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
            ]);
        }

        return redirect()->route('admin.products.detail-values.index', $product)
            ->with('success', 'Product detail value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetailValue $detailValue)
    {
        $detailValue->translates()->delete();
        $detailValue->delete();

        return redirect()->route('admin.products.detail-values.index', $product)
            ->with('success', 'Product detail value deleted successfully.');
    }

    /**
     * Reorder product detail values
     */
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:product_detail_values,id'
        ]);

        foreach ($request->items as $index => $id) {
            ProductDetailValue::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete product detail values
     */
    public function bulkDelete(Request $request, Product $product)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_detail_values,id'
        ]);

        $detailValues = ProductDetailValue::whereIn('id', $request->ids)->get();
        
        foreach ($detailValues as $detailValue) {
            $detailValue->translates()->delete();
            $detailValue->delete();
        }

        return response()->json(['success' => true]);
    }
}
