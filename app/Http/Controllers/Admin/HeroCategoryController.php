<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroCategory;
use Illuminate\Http\Request;
use App\Repositories\HeroCategoryRepository;
use App\Http\Requests\Admin\HeroCategory\StoreHeroCategoryRequest;
use App\Http\Requests\Admin\HeroCategory\UpdateHeroCategoryRequest;

class HeroCategoryController extends Controller
{
    public function __construct(public HeroCategoryRepository $repository)
    {
    }

    public function index()
    {
        $heroCategories = $this->repository->getHeroCategories();
        $allCategories = $this->repository->getAvailableCategories($heroCategories);
        return view('admin.hero-categories.index', compact('heroCategories', 'allCategories'));
    }

    public function create()
    {
        $categories = $this->repository->getCategoriesForCreate();
        return view('admin.hero-categories.create', compact('categories'));
    }

    public function store(StoreHeroCategoryRequest $request)
    {
        try {
            $this->repository->createHeroCategory([
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
        $categories = $this->repository->getCategoriesForEdit($heroCategory);
        return view('admin.hero-categories.edit', compact('heroCategory', 'categories'));
    }

    public function update(UpdateHeroCategoryRequest $request, HeroCategory $heroCategory)
    {
        try {
            $this->repository->updateHeroCategory($heroCategory, [
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
            $this->repository->deleteHeroCategory($heroCategory);
            return redirect()->route('admin.hero-categories.index')
                ->with('success', __('Hero category deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:hero_categories,id'
        ]);

        try {
            $this->repository->reorderHeroCategories($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
