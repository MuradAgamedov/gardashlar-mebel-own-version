<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroCategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeroCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heroCategories = HeroCategory::with('productCategory.translates.language')
            ->ordered()
            ->get();
            
        $allCategories = ProductCategory::with('translates.language')
            ->whereNotIn('id', $heroCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
            
        return view('admin.hero-categories.index', compact('heroCategories', 'allCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::with('translates.language')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('hero_categories')
                      ->whereRaw('hero_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
            
        return view('admin.hero-categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id|unique:hero_categories,product_category_id',
            'status' => 'boolean'
        ]);

        try {
            HeroCategory::create([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-categories.index')
                ->with('success', 'Category added to hero section successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HeroCategory $heroCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HeroCategory $heroCategory)
    {
        $categories = ProductCategory::with('translates.language')
            ->where(function ($query) use ($heroCategory) {
                $query->where('id', $heroCategory->product_category_id)
                      ->orWhereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                                   ->from('hero_categories')
                                   ->whereRaw('hero_categories.product_category_id = product_categories.id');
                      });
            })
            ->ordered()
            ->get();
            
        return view('admin.hero-categories.edit', compact('heroCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HeroCategory $heroCategory)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id|unique:hero_categories,product_category_id,' . $heroCategory->id,
            'status' => 'boolean'
        ]);

        try {
            $heroCategory->update([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-categories.index')
                ->with('success', 'Hero category updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeroCategory $heroCategory)
    {
        try {
            $heroCategory->delete();

            return redirect()->route('admin.hero-categories.index')
                ->with('success', 'Hero category deleted successfully.');

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
            'items.*' => 'required|integer|exists:hero_categories,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                HeroCategory::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
