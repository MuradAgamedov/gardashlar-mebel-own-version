<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetailValue;
use App\Models\ProductDetailsValuesDetail;
use App\Models\ProductDetailsValuesDetailTranslate;
use App\Models\Language;
use Illuminate\Http\Request;

class ProductDetailsValuesDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product, ProductDetailValue $detailValue)
    {
        $details = ProductDetailsValuesDetail::with(['translates.language'])
            ->where('product_detail_value_id', $detailValue->id)
            ->ordered()
            ->get();
            
        return view('admin.product-details-values-details.index', compact('product', 'detailValue', 'details'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product, ProductDetailValue $detailValue)
    {
        $languages = Language::all();
        return view('admin.product-details-values-details.create', compact('product', 'detailValue', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product, ProductDetailValue $detailValue)
    {
        $request->validate([
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.keyword' => 'required|string|max:255',
            'translates.*.value' => 'required|string',
        ]);

        // Create detail
        $detail = ProductDetailsValuesDetail::create([
            'product_detail_value_id' => $detailValue->id,
            'order' => ProductDetailsValuesDetail::where('product_detail_value_id', $detailValue->id)->max('order') + 1
        ]);

        // Create translations
        foreach ($request->translates as $translate) {
            ProductDetailsValuesDetailTranslate::create([
                'product_details_values_detail_id' => $detail->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
                'value' => $translate['value'],
            ]);
        }

        return redirect()->route('admin.products.detail-values.details.index', [$product, $detailValue])
            ->with('success', 'Detail created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, ProductDetailValue $detailValue, ProductDetailsValuesDetail $detail)
    {
        $detail->load(['translates.language']);
        return view('admin.product-details-values-details.show', compact('product', 'detailValue', 'detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductDetailValue $detailValue, ProductDetailsValuesDetail $detail)
    {
        $languages = Language::all();
        $detail->load(['translates.language']);
        return view('admin.product-details-values-details.edit', compact('product', 'detailValue', 'detail', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductDetailValue $detailValue, ProductDetailsValuesDetail $detail)
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
            ProductDetailsValuesDetailTranslate::create([
                'product_details_values_detail_id' => $detail->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
                'value' => $translate['value'],
            ]);
        }

        return redirect()->route('admin.products.detail-values.details.index', [$product, $detailValue])
            ->with('success', 'Detail updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetailValue $detailValue, ProductDetailsValuesDetail $detail)
    {
        $detail->translates()->delete();
        $detail->delete();

        return redirect()->route('admin.products.detail-values.details.index', [$product, $detailValue])
            ->with('success', 'Detail deleted successfully.');
    }

    /**
     * Reorder details
     */
    public function reorder(Request $request, Product $product, ProductDetailValue $detailValue)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:product_details_values_details,id'
        ]);

        foreach ($request->items as $index => $id) {
            ProductDetailsValuesDetail::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete details
     */
    public function bulkDelete(Request $request, Product $product, ProductDetailValue $detailValue)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:product_details_values_details,id'
            ]);

            $details = ProductDetailsValuesDetail::whereIn('id', $request->ids)->get();
            
            foreach ($details as $detail) {
                $detail->translates()->delete();
                $detail->delete();
            }

            return response()->json(['success' => true, 'message' => 'Selected details deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error occurred while deleting: ' . $e->getMessage()], 500);
        }
    }
}
