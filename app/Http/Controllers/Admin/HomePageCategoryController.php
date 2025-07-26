<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageCategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homePageCategories = HomePageCategory::with('productCategory.translates.language')
            ->ordered()
            ->get();
            
        $allCategories = ProductCategory::with('translates.language')
            ->whereNotIn('id', $homePageCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
            
        return view('admin.home-page-categories.index', compact('homePageCategories', 'allCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::with('translates.language')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('home_page_categories')
                      ->whereRaw('home_page_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
            
        return view('admin.home-page-categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id|unique:home_page_categories,product_category_id',
            'status' => 'boolean'
        ]);

        try {
            HomePageCategory::create([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', 'Category added to home page successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HomePageCategory $homePageCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomePageCategory $homePageCategory)
    {
        $categories = ProductCategory::with('translates.language')
            ->where(function ($query) use ($homePageCategory) {
                $query->where('id', $homePageCategory->product_category_id)
                      ->orWhereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                                   ->from('home_page_categories')
                                   ->whereRaw('home_page_categories.product_category_id = product_categories.id');
                      });
            })
            ->ordered()
            ->get();
            
        return view('admin.home-page-categories.edit', compact('homePageCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomePageCategory $homePageCategory)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id|unique:home_page_categories,product_category_id,' . $homePageCategory->id,
            'status' => 'boolean'
        ]);

        try {
            $homePageCategory->update([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', 'Home page category updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomePageCategory $homePageCategory)
    {
        try {
            $homePageCategory->delete();

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', 'Home page category deleted successfully.');

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
            'items.*' => 'required|integer|exists:home_page_categories,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                HomePageCategory::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
