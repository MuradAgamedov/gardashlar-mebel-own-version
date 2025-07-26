<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPageCategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductPageCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productPageCategories = ProductPageCategory::with('productCategory.translates.language')
            ->ordered()
            ->get();
            
        $allCategories = ProductCategory::with('translates.language')
            ->whereNotIn('id', $productPageCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
            
        return view('admin.product-page-categories.index', compact('productPageCategories', 'allCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::with('translates.language')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('product_page_categories')
                      ->whereRaw('product_page_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
            
        return view('admin.product-page-categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id|unique:product_page_categories,product_category_id',
            'status' => 'boolean'
        ]);

        try {
            ProductPageCategory::create([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.product-page-categories.index')
                ->with('success', 'Category added to product page successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
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
        $categories = ProductCategory::with('translates.language')
            ->where(function ($query) use ($productPageCategory) {
                $query->where('id', $productPageCategory->product_category_id)
                      ->orWhereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                                   ->from('product_page_categories')
                                   ->whereRaw('product_page_categories.product_category_id = product_categories.id');
                      });
            })
            ->ordered()
            ->get();
            
        return view('admin.product-page-categories.edit', compact('productPageCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPageCategory $productPageCategory)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id|unique:product_page_categories,product_category_id,' . $productPageCategory->id,
            'status' => 'boolean'
        ]);

        try {
            $productPageCategory->update([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.product-page-categories.index')
                ->with('success', 'Product page category updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
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
                ->with('success', 'Product page category deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:product_page_categories,id'
        ]);

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
