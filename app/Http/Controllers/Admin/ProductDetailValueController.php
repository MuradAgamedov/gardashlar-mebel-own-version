<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetailValue;
use App\Models\Language;
use App\Repositories\ProductDetailValueRepository;
use Illuminate\Http\Request;

class ProductDetailValueController extends Controller
{
    protected $repository;

    public function __construct(ProductDetailValueRepository $repository)
    {
        $this->repository = $repository;
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
        ]);

        $this->repository->createValue($request->all(), $product->id);

        return redirect()->route('admin.products.detail-values.index', $product)
            ->with('success', 'Product detail value created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductDetailValue $detailValue)
    {
        $request->validate([
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.keyword' => 'required|string|max:255',
        ]);

        $this->repository->updateValue($detailValue, $request->all());

        return redirect()->route('admin.products.detail-values.index', $product)
            ->with('success', 'Product detail value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetailValue $detailValue)
    {
        $this->repository->deleteValue($detailValue);

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

        $this->repository->reorder($request->items);

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete product detail values
     */
    public function bulkDelete(Request $request, Product $product)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:product_detail_values,id'
            ]);

            $this->repository->bulkDelete($request->ids);

            return response()->json([
                'success' => true, 
                'message' => 'Selected detail values deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error occurred while deleting: ' . $e->getMessage()
            ], 500);
        }
    }
}
