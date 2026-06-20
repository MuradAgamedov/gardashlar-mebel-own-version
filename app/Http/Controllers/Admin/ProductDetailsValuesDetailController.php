<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductDetailsValuesDetail\BulkDeleteProductDetailsValuesDetailRequest;

use App\Http\Requests\Admin\ProductDetailsValuesDetail\ReorderProductDetailsValuesDetailRequest;

use App\Http\Requests\Admin\ProductDetailsValuesDetail\StoreProductDetailsValuesDetailRequest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetailValue;
use App\Models\ProductDetailsValuesDetail;
use App\Models\ProductDetailsValuesDetailTranslate;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class ProductDetailsValuesDetailController extends Controller
{
    protected $repository;

    public function __construct(protected LanguageServiceInterface $languageService, protected ProductDetailsValuesDetailServiceInterface $productDetailsValuesDetailService)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product, ProductDetailValue $detailValue)
    {
        $details = $this->productDetailsValuesDetailService->getDetailsByValueId($detailValue->id);
        return view('admin.product-details-values-details.index', compact('product', 'detailValue', 'details'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product, ProductDetailValue $detailValue)
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.product-details-values-details.create', compact('product', 'detailValue', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductDetailsValuesDetailRequest $request, Product $product, ProductDetailValue $detailValue)
    {
        $request->validated();

        // Create a separate detail for each keyword-value pair
        foreach ($request->translates as $translate) {
            // Create detail
            $detail = ProductDetailsValuesDetail::create([
                'product_detail_value_id' => $detailValue->id,
                'order' => ProductDetailsValuesDetail::where('product_detail_value_id', $detailValue->id)->max('order') + 1
            ]);

            // Create translation for this detail
            ProductDetailsValuesDetailTranslate::create([
                'product_details_values_detail_id' => $detail->id,
                'language_id' => $translate['language_id'],
                'keyword' => $translate['keyword'],
                'value' => $translate['value'],
            ]);
        }

        return redirect()->route('admin.products.detail-values.details.index', [$product, $detailValue])
            ->with('success', 'Details created successfully.');
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
        $languages = $this->languageService->getAllLanguages();
        $detail->load(['translates.language']);
        return view('admin.product-details-values-details.edit', compact('product', 'detailValue', 'detail', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductDetailValue $detailValue, ProductDetailsValuesDetail $detail)
    {
        $request->validated();

        // Since each detail now has only one translation, we'll update the first one
        $translate = $request->translates[0];
        
        // Delete existing translations
        $detail->translates()->delete();

        // Create new translation
        ProductDetailsValuesDetailTranslate::create([
            'product_details_values_detail_id' => $detail->id,
            'language_id' => $translate['language_id'],
            'keyword' => $translate['keyword'],
            'value' => $translate['value'],
        ]);

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
    public function reorder(ReorderProductDetailsValuesDetailRequest $request, Product $product, ProductDetailValue $detailValue)
    {
        $request->validated();

        foreach ($request->items as $index => $id) {
            ProductDetailsValuesDetail::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete details
     */
    public function bulkDelete(BulkDeleteProductDetailsValuesDetailRequest $request, Product $product, ProductDetailValue $detailValue)
    {
        try {
            $request->validated();

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
