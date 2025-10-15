<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPriceFilter;
use App\Models\ProductPriceFilterTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\ProductPriceFilterRepository;
use App\Repositories\LanguageRepository;

class ProductPriceFilterController extends Controller
{
    public function __construct(
        public ProductPriceFilterRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = $this->repository->getFilters();

        return view('admin.product-price-filters.index', compact('filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.product-price-filters.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'translates' => 'required|array',
            'translates.*.title' => 'required|string|max:255',
            'status' => 'boolean'
        ]);
    
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
                ->with('success', 'Qiymət filtiri uğurla əlavə edildi.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
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
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.product-price-filters.edit', compact('productPriceFilter', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPriceFilter $productPriceFilter)
    {
        $request->validate([
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'translates' => 'required|array',
            'translates.*.title' => 'required|string|max:255',
            'status' => 'boolean'
        ]);

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
                ->with('success', 'Qiymət filtiri uğurla yeniləndi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
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
                ->with('success', 'Qiymət filtiri uğurla silindi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Reorder filters
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:product_price_filters,id'
        ]);

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
