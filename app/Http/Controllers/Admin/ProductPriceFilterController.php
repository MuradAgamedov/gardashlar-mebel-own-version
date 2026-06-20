<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductPriceFilter\ReorderProductPriceFilterRequest;

use App\Http\Requests\Admin\ProductPriceFilter\StoreProductPriceFilterRequest;

use App\Http\Controllers\Controller;
use App\Models\ProductPriceFilter;
use App\Models\ProductPriceFilterTranslate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Services\Admin\ProductPriceFilterServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class ProductPriceFilterController extends Controller
{
    public function __construct(
        protected ProductPriceFilterServiceInterface $productPriceFilterService,
        protected LanguageServiceInterface $languageService
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = $this->productPriceFilterService->getFilters();

        return view('admin.product-price-filters.index', compact('filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.product-price-filters.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductPriceFilterRequest $request)
    {
        $request->validated();
    
        try {
            DB::beginTransaction();
    
            // Əsas filter-i yarad
            $filter = ProductPriceFilter::create([
                'min_price' => $request->input('min_price') ?: null,
                'max_price' => $request->input('max_price') ?: null,
                'status' => $request->boolean('status', true)
            ]);
           
    
            // Tərcümələri əlavə et
            foreach ($request->input('translates') as $languageId => $translate) {
                ProductPriceFilterTranslate::create([
                    'product_price_filter_id' => $filter->id,
                    'language_id' => $languageId,
                    'title' => $translate['title'],
                ]);
            }
    
            DB::commit();
    
            return redirect()->route('admin.product-price-filters.index')
                ->with('success', __('Price filter created successfully!'));
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error creating price filter: :error', ['error' => $e->getMessage()]));
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPriceFilter $productPriceFilter)
    {
        $productPriceFilter->load('translates');
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.product-price-filters.edit', compact('productPriceFilter', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPriceFilter $productPriceFilter)
    {
        $request->validated();

        try {
            DB::beginTransaction();

            // Update filter
            $productPriceFilter->update([
                'min_price' => $request->input('min_price') ?: null,
                'max_price' => $request->input('max_price') ?: null,
                'status' => $request->input('status', true)
            ]);

            // Delete existing translates
            $productPriceFilter->translates()->delete();

            // Create new translates
            foreach ($request->input('translates') as $languageId => $translate) {
                ProductPriceFilterTranslate::create([
                    'product_price_filter_id' => $productPriceFilter->id,
                    'language_id' => $languageId,
                    'title' => $translate['title'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.product-price-filters.index')
                ->with('success', __('Price filter updated successfully!'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('Error updating price filter: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPriceFilter $productPriceFilter)
    {
        try {
            DB::beginTransaction();

            // Delete translates first
            $productPriceFilter->translates()->delete();
            
            // Delete filter
            $productPriceFilter->delete();

            DB::commit();

            return redirect()->route('admin.product-price-filters.index')
                ->with('success', __('Price filter deleted successfully!'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('Error deleting price filter: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reorder filters
     */
    public function reorder(ReorderProductPriceFilterRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->input('items') as $index => $id) {
                ProductPriceFilter::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
