<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageCategory;
use Illuminate\Http\Request;
use App\Repositories\HomePageCategoryRepository;
use App\Http\Requests\Admin\HomePageCategory\StoreHomePageCategoryRequest;
use App\Http\Requests\Admin\HomePageCategory\UpdateHomePageCategoryRequest;

class HomePageCategoryController extends Controller
{
    public function __construct(public HomePageCategoryRepository $repository)
    {
    }

    public function index()
    {
        $homePageCategories = $this->repository->getHomePageCategories();
        $allCategories = $this->repository->getAvailableCategories($homePageCategories);
        return view('admin.home-page-categories.index', compact('homePageCategories', 'allCategories'));
    }

    public function create()
    {
        $categories = $this->repository->getCategoriesForCreate();
        return view('admin.home-page-categories.create', compact('categories'));
    }

    public function store(StoreHomePageCategoryRequest $request)
    {
        try {
            $this->repository->createHomePageCategory([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', 'Category added to home page successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function show(HomePageCategory $homePageCategory)
    {
        //
    }

    public function edit(HomePageCategory $homePageCategory)
    {
        $categories = $this->repository->getCategoriesForEdit($homePageCategory);
        return view('admin.home-page-categories.edit', compact('homePageCategory', 'categories'));
    }

    public function update(UpdateHomePageCategoryRequest $request, HomePageCategory $homePageCategory)
    {
        try {
            $this->repository->updateHomePageCategory($homePageCategory, [
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status', true)
            ]);

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', 'Home page category updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(HomePageCategory $homePageCategory)
    {
        try {
            $this->repository->deleteHomePageCategory($homePageCategory);
            return redirect()->route('admin.home-page-categories.index')
                ->with('success', 'Home page category deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:home_page_categories,id'
        ]);

        try {
            $this->repository->reorderHomePageCategories($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
