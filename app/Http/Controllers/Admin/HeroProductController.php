<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\HeroProductRepository;

class HeroProductController extends Controller
{
    public function __construct(public HeroProductRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heroProducts = $this->repository->getHeroProducts();
            
        return view('admin.hero-products.index', compact('heroProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.hero-products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:hero_products,product_id',
            'status' => 'boolean'
        ]);

        try {
            HeroProduct::create([
                'product_id' => $request->input('product_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Product added to hero section successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HeroProduct $heroProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HeroProduct $heroProduct)
    {
        return view('admin.hero-products.edit', compact('heroProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HeroProduct $heroProduct)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:hero_products,product_id,' . $heroProduct->id,
            'status' => 'boolean'
        ]);

        try {
            $heroProduct->update([
                'product_id' => $request->input('product_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Hero product updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeroProduct $heroProduct)
    {
        try {
            $heroProduct->delete();

            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Hero product deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reorder products
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:hero_products,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                HeroProduct::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Search products for AJAX request
     */
    public function searchProducts(Request $request)
    {
        $search = $request->input('search');
        $excludeIds = $request->input('exclude_ids', []);

        $products = $this->repository->searchProducts($search, $excludeIds);

        $results = [];
        foreach ($products as $product) {
            $firstTranslation = $product->translates->first();
            $firstCategory = $product->categories->first();
            $categoryTranslation = $firstCategory ? $firstCategory->translates->first() : null;
            
            $results[] = [
                'id' => $product->id,
                'title' => $firstTranslation ? $firstTranslation->title : 'No title',
                'category' => $categoryTranslation ? $categoryTranslation->title : 'No category',
                'text' => ($firstTranslation ? $firstTranslation->title : 'No title') . 
                         ' - ' . ($categoryTranslation ? $categoryTranslation->title : 'No category')
            ];
        }

        return response()->json($results);
    }
} 