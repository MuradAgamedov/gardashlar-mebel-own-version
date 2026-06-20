<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\HomePageProjectCategory\ReorderHomePageProjectCategoryRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Interfaces\Services\Admin\HomePageProjectCategoryServiceInterface;
use App\Http\Requests\Admin\HomePageProjectCategory\StoreHomePageProjectCategoryRequest;
use App\Http\Requests\Admin\HomePageProjectCategory\UpdateHomePageProjectCategoryRequest;

class HomePageProjectCategoryController extends Controller
{
    public function __construct(protected HomePageProjectCategoryServiceInterface $homePageProjectCategoryService)
    {
    }

    public function index(): View
    {
        $homePageProjectCategories = $this->homePageProjectCategoryService->getHomePageProjectCategories();
        return view('admin.home-page-project-categories.index', compact('homePageProjectCategories'));
    }

    public function create(): View
    {
        $projectCategories = $this->homePageProjectCategoryService->getActiveProjectCategories();
        return view('admin.home-page-project-categories.create', compact('projectCategories'));
    }

    public function store(StoreHomePageProjectCategoryRequest $request): RedirectResponse
    {
        $this->homePageProjectCategoryService->createHomePageProjectCategory([
            'project_category_id' => $request->project_category_id,
            'status' => $request->status == '1',
        ]);

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', __('Project category added to home page successfully.'));
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id): View
    {
        $homePageProjectCategory = $this->homePageProjectCategoryService->findWithCategory($id);
        $projectCategories = $this->homePageProjectCategoryService->getActiveProjectCategories();
        return view('admin.home-page-project-categories.edit', compact('homePageProjectCategory', 'projectCategories'));
    }

    public function update(UpdateHomePageProjectCategoryRequest $request, string $id): RedirectResponse
    {
        $this->homePageProjectCategoryService->updateHomePageProjectCategory($id, [
            'project_category_id' => $request->project_category_id,
            'status' => $request->status == '1',
        ]);

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', __('Project category updated successfully.'));
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->homePageProjectCategoryService->deleteHomePageProjectCategory($id);
        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', __('Project category removed from home page successfully.'));
    }

    public function reorder(ReorderHomePageProjectCategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->validated();

        $this->homePageProjectCategoryService->reorderHomePageProjectCategories($request->items);
        return response()->json(['success' => true, 'message' => __('Order updated successfully')]);
    }
}
