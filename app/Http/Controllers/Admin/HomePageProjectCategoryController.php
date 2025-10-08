<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePageProjectCategory;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\HomePageProjectCategoryRepository;

class HomePageProjectCategoryController extends Controller
{
    public function __construct(public HomePageProjectCategoryRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $homePageProjectCategories = $this->repository->getHomePageProjectCategories();

        return view('admin.home-page-project-categories.index', compact('homePageProjectCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projectCategories = $this->repository->getActiveProjectCategories();

        return view('admin.home-page-project-categories.create', compact('projectCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_category_id' => 'required|exists:project_categories,id|unique:home_page_project_categories,project_category_id',
        ]);

        HomePageProjectCategory::create([
            'project_category_id' => $request->project_category_id,
            'status' => true,
        ]);

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', 'Project category added to home page successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not needed for this resource
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $homePageProjectCategory = HomePageProjectCategory::with('projectCategory.translates')->findOrFail($id);
        $projectCategories = $this->repository->getActiveProjectCategories();

        return view('admin.home-page-project-categories.edit', compact('homePageProjectCategory', 'projectCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $homePageProjectCategory = HomePageProjectCategory::findOrFail($id);

        $request->validate([
            'project_category_id' => 'required|exists:project_categories,id|unique:home_page_project_categories,project_category_id,' . $id,
            'status' => 'boolean',
        ]);

        $homePageProjectCategory->update([
            'project_category_id' => $request->project_category_id,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', 'Project category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $homePageProjectCategory = HomePageProjectCategory::findOrFail($id);
        $homePageProjectCategory->delete();

        return redirect()->route('admin.home-page-project-categories.index')
            ->with('success', 'Project category removed from home page successfully.');
    }

    /**
     * Reorder home page project categories
     */
    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:home_page_project_categories,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            HomePageProjectCategory::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }
}
