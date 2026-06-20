<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\HeroProduct\ReorderHeroProductRequest;

use App\Http\Controllers\Controller;
use App\Models\HeroProduct;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\HeroProductServiceInterface;
use App\Http\Requests\Admin\HeroProduct\StoreHeroProductRequest;
use App\Http\Requests\Admin\HeroProduct\UpdateHeroProductRequest;

class HeroProductController extends Controller
{
    public function __construct(protected HeroProductServiceInterface $heroProductService)
    {
    }

    public function index()
    {
        $heroProducts = $this->heroProductService->getHeroProducts();
        return view('admin.hero-products.index', compact('heroProducts'));
    }

    public function create()
    {
        return view('admin.hero-products.create');
    }

    public function store(StoreHeroProductRequest $request)
    {
        try {
            $this->heroProductService->createHeroProduct([
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
            $this->heroProductService->updateHeroProduct($heroProduct, [
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
            $this->heroProductService->deleteHeroProduct($heroProduct);
            return redirect()->route('admin.hero-products.index')
                ->with('success', 'Hero product deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderHeroProductRequest $request)
    {
        $request->validated();

        try {
            $this->heroProductService->reorderHeroProducts($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function searchProducts(Request $request)
    {
        $search = $request->input('search');
        $excludeIds = $request->input('exclude_ids', []);
        $products = $this->heroProductService->searchProducts($search, $excludeIds);
        $results = $this->heroProductService->formatSearchResults($products);
        return response()->json($results);
    }
} 