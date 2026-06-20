<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\HeroCategory\ReorderHeroCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\HeroCategory;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\HeroCategoryServiceInterface;
use App\Http\Requests\Admin\HeroCategory\StoreHeroCategoryRequest;
use App\Http\Requests\Admin\HeroCategory\UpdateHeroCategoryRequest;

class HeroCategoryController extends Controller
{
    public function __construct(protected HeroCategoryServiceInterface $heroCategoryService)
    {
    }

    public function index()
    {
        $heroCategories = $this->heroCategoryService->getHeroCategories();
        $allCategories = $this->heroCategoryService->getAvailableCategories($heroCategories);
        return view('admin.hero-categories.index', compact('heroCategories', 'allCategories'));
    }

    public function create()
    {
        $categories = $this->heroCategoryService->getCategoriesForCreate();
        return view('admin.hero-categories.create', compact('categories'));
    }

    public function store(StoreHeroCategoryRequest $request)
    {
        try {
            $this->heroCategoryService->createHeroCategory([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-categories.index')
                ->with('success', __('Category added to hero section successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function show(HeroCategory $heroCategory)
    {
        //
    }

    public function edit(HeroCategory $heroCategory)
    {
        $categories = $this->heroCategoryService->getCategoriesForEdit($heroCategory);
        return view('admin.hero-categories.edit', compact('heroCategory', 'categories'));
    }

    public function update(UpdateHeroCategoryRequest $request, HeroCategory $heroCategory)
    {
        try {
            $this->heroCategoryService->updateHeroCategory($heroCategory, [
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.hero-categories.index')
                ->with('success', __('Hero category updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(HeroCategory $heroCategory)
    {
        try {
            $this->heroCategoryService->deleteHeroCategory($heroCategory);
            return redirect()->route('admin.hero-categories.index')
                ->with('success', __('Hero category deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderHeroCategoryRequest $request)
    {
        $request->validated();

        try {
            $this->heroCategoryService->reorderHeroCategories($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
