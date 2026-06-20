<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\HomePageCategory\ReorderHomePageCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\HomePageCategory;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\HomePageCategoryServiceInterface;
use App\Http\Requests\Admin\HomePageCategory\StoreHomePageCategoryRequest;
use App\Http\Requests\Admin\HomePageCategory\UpdateHomePageCategoryRequest;

class HomePageCategoryController extends Controller
{
    public function __construct(protected HomePageCategoryServiceInterface $homePageCategoryService)
    {
    }

    public function index()
    {
        $homePageCategories = $this->homePageCategoryService->getHomePageCategories();
        $allCategories = $this->homePageCategoryService->getAvailableCategories($homePageCategories);
        return view('admin.home-page-categories.index', compact('homePageCategories', 'allCategories'));
    }

    public function create()
    {
        $categories = $this->homePageCategoryService->getCategoriesForCreate();
        return view('admin.home-page-categories.create', compact('categories'));
    }

    public function store(StoreHomePageCategoryRequest $request)
    {
        try {
            $this->homePageCategoryService->createHomePageCategory([
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status')
            ]);

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', __('Category added to home page successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function show(HomePageCategory $homePageCategory)
    {
        //
    }

    public function edit(HomePageCategory $homePageCategory)
    {
        $categories = $this->homePageCategoryService->getCategoriesForEdit($homePageCategory);
        return view('admin.home-page-categories.edit', compact('homePageCategory', 'categories'));
    }

    public function update(UpdateHomePageCategoryRequest $request, HomePageCategory $homePageCategory)
    {
        try {
            $this->homePageCategoryService->updateHomePageCategory($homePageCategory, [
                'product_category_id' => $request->input('product_category_id'),
                'status' => $request->boolean('status')
            ]);

            return redirect()->route('admin.home-page-categories.index')
                ->with('success', __('Home page category updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(HomePageCategory $homePageCategory)
    {
        try {
            $this->homePageCategoryService->deleteHomePageCategory($homePageCategory);
            return redirect()->route('admin.home-page-categories.index')
                ->with('success', __('Home page category deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderHomePageCategoryRequest $request)
    {
        $request->validated();

        try {
            $this->homePageCategoryService->reorderHomePageCategories($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
