<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroProduct;
use Illuminate\Http\Request;
use App\Repositories\HeroProductRepository;
use App\Http\Requests\Admin\HeroProduct\StoreHeroProductRequest;
use App\Http\Requests\Admin\HeroProduct\UpdateHeroProductRequest;

class HeroProductController extends Controller
{
    public function __construct(public HeroProductRepository $repository)
    {
    }

    public function index()
    {
        $heroProducts = $this->repository->getHeroProducts();
        return view('admin.hero-products.index', compact('heroProducts'));
    }

    public function create()
    {
        return view('admin.hero-products.create');
    }

    public function store(StoreHeroProductRequest $request)
    {
        try {
            $this->repository->createHeroProduct([
                'product_id' => $request->input('product_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Product added to hero section successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function show(HeroProduct $heroProduct)
    {
        //
    }

    public function edit(HeroProduct $heroProduct)
    {
        return view('admin.hero-products.edit', compact('heroProduct'));
    }

    public function update(UpdateHeroProductRequest $request, HeroProduct $heroProduct)
    {
        try {
            $this->repository->updateHeroProduct($heroProduct, [
                'product_id' => $request->input('product_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Hero product updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(HeroProduct $heroProduct)
    {
        try {
            $this->repository->deleteHeroProduct($heroProduct);
            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Hero product deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:hero_products,id'
        ]);

        try {
            $this->repository->reorderHeroProducts($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function searchProducts(Request $request)
    {
        $search = $request->input('search');
        $excludeIds = $request->input('exclude_ids', []);
        $products = $this->repository->searchProducts($search, $excludeIds);
        $results = $this->repository->formatSearchResults($products);
        return response()->json($results);
    }
} 