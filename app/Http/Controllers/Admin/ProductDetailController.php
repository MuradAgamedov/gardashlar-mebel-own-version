<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailTranslate;
use App\Models\Language;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $details = $product->details()->with(['translates.language'])->ordered()->get();
        return view('admin.product-details.index', compact('product', 'details'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $languages = Language::all();
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

        // Create product detail
        $productDetail = ProductDetail::create([
            'product_id' => $product->id,
            'order' => ProductDetail::where('product_id', $product->id)->max('order') + 1
        ]);

        // Create translations
        foreach ($request->translates as $translate) {
            ProductDetailTranslate::create([
                'product_detail_id' => $productDetail->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
                'value' => $translate['value'],
            ]);
        }

        return redirect()->route('admin.products.details.index', $product)
            ->with('success', 'Product detail created successfully.');
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
        $languages = Language::all();
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

        // Delete existing translations
        $detail->translates()->delete();

        // Create new translations
        foreach ($request->translates as $translate) {
            ProductDetailTranslate::create([
                'product_detail_id' => $detail->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
                'value' => $translate['value'],
            ]);
        }

        return redirect()->route('admin.products.details.index', $product)
            ->with('success', 'Product detail updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetail $detail)
    {
        $detail->translates()->delete();
        $detail->delete();

        return redirect()->route('admin.products.details.index', $product)
            ->with('success', 'Product detail deleted successfully.');
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
            ProductDetail::where('id', $id)->update(['order' => $index + 1]);
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

        $details = ProductDetail::whereIn('id', $request->ids)->get();
        
        foreach ($details as $detail) {
            $detail->translates()->delete();
            $detail->delete();
        }

        return response()->json(['success' => true]);
    }
}
