<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductPageCategory\ReorderProductPageCategoryRequest;

use App\Http\Requests\Admin\ProductPageCategory\UpdateProductPageCategoryRequest;

use App\Http\Requests\Admin\ProductPageCategory\StoreProductPageCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\ProductPageCategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Services\Admin\ProductPageCategoryServiceInterface;

class ProductPageCategoryController extends Controller
{
    public function __construct(protected ProductPageCategoryServiceInterface $productPageCategoryService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productPageCategories = $this->productPageCategoryService->getProductPageCategories();
        $allCategories = $this->productPageCategoryService->getUnusedCategories($productPageCategories->pluck('product_category_id'));
            
        return view('admin.product-page-categories.index', compact('productPageCategories', 'allCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->productPageCategoryService->getCategoriesNotInProductPage();
            
        return view('admin.product-page-categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductPageCategoryRequest $request)
    {
        $request->validated();

        try {
            ProductPageCategory::create([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.product-page-categories.index')
                ->with('success', __('Category added to product page successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPageCategory $productPageCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPageCategory $productPageCategory)
    {
        $categories = $this->productPageCategoryService->getCategoriesForEdit($productPageCategory->product_category_id);
            
        return view('admin.product-page-categories.edit', compact('productPageCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductPageCategoryRequest $request, ProductPageCategory $productPageCategory)
    {
        $request->validated();

        try {
            $productPageCategory->update([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.product-page-categories.index')
                ->with('success', __('Product page category updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPageCategory $productPageCategory)
    {
        try {
            $productPageCategory->delete();

            return redirect()->route('admin.product-page-categories.index')
                ->with('success', __('Product page category deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reorder categories
     */
    public function reorder(ReorderProductPageCategoryRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->input('items') as $index => $id) {
                ProductPageCategory::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
