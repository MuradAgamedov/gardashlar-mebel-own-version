<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\HomePageProjectCategoryRepository;
use App\Http\Requests\Admin\HomePageProjectCategory\StoreHomePageProjectCategoryRequest;
use App\Http\Requests\Admin\HomePageProjectCategory\UpdateHomePageProjectCategoryRequest;

class HomePageProjectCategoryController extends Controller
{
    public function __construct(public HomePageProjectCategoryRepository $repository)
    {
    }

    public function index(): View
    {
        $homePageProjectCategories = $this->repository->getHomePageProjectCategories();
        return view('admin.home-page-project-categories.index', compact('homePageProjectCategories'));
    }

    public function create(): View
    {
        $projectCategories = $this->repository->getActiveProjectCategories();
        return view('admin.home-page-project-categories.create', compact('projectCategories'));
    }

    public function store(StoreHomePageProjectCategoryRequest $request): RedirectResponse
    {
        $this->repository->createHomePageProjectCategory([
            'project_category_id' => $request->project_category_id,
            'status' => $request->status == '1',
        ]);

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', 'Project category added to home page successfully.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id): View
    {
        $homePageProjectCategory = $this->repository->findWithCategory($id);
        $projectCategories = $this->repository->getActiveProjectCategories();
        return view('admin.home-page-project-categories.edit', compact('homePageProjectCategory', 'projectCategories'));
    }

    public function update(UpdateHomePageProjectCategoryRequest $request, string $id): RedirectResponse
    {
        $this->repository->updateHomePageProjectCategory($id, [
            'project_category_id' => $request->project_category_id,
            'status' => $request->status == '1',
        ]);

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', 'Project category updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->repository->deleteHomePageProjectCategory($id);
        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', 'Project category removed from home page successfully.');
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:home_page_project_categories,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        $this->repository->reorderHomePageProjectCategories($request->items);
        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }
}
