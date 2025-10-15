<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetailValue;
use App\Models\ProductDetailValueTranslate;
use App\Models\Language;
use App\Repositories\ProductDetailValueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($request, $product) {
            $detailValue = ProductDetailValue::create([
                'product_id' => $product->id,
                'order' => ProductDetailValue::where('product_id', $product->id)->max('order') + 1
            ]);

            foreach ($request->translates as $translation) {
                ProductDetailValueTranslate::create([
                    'product_detail_value_id' => $detailValue->id,
                    'language_id' => $translation['language_id'],
                    'keyword' => $translation['keyword'],
                ]);
            }
        });

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

        DB::transaction(function () use ($request, $detailValue) {
            $detailValue->translates()->delete();
            
            foreach ($request->translates as $translation) {
                ProductDetailValueTranslate::create([
                    'product_detail_value_id' => $detailValue->id,
                    'language_id' => $translation['language_id'],
                    'keyword' => $translation['keyword'],
                ]);
            }
        });

        return redirect()->route('admin.products.detail-values.index', $product)
            ->with('success', 'Product detail value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductDetailValue $detailValue)
    {
        DB::transaction(function () use ($detailValue) {
            $detailValue->translates()->delete();
            $detailValue->delete();
        });

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

        DB::transaction(function () use ($request) {
            foreach ($request->items as $index => $id) {
                ProductDetailValue::where('id', $id)->update(['order' => $index + 1]);
            }
        });

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

            DB::transaction(function () use ($request) {
                $detailValues = ProductDetailValue::whereIn('id', $request->ids)->get();
                
                foreach ($detailValues as $detailValue) {
                    $detailValue->translates()->delete();
                    $detailValue->delete();
                }
            });

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
