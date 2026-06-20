<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProjectCategory\BulkDeleteProjectCategoryRequest;

use App\Http\Requests\Admin\ProjectCategory\ReorderProjectCategoryRequest;

use App\Http\Requests\Admin\ProjectCategory\StoreProjectCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\ImageService;
use App\Interfaces\Services\Admin\ProjectCategoryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class ProjectCategoryController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected ProjectCategoryServiceInterface $projectCategoryService,
        protected LanguageServiceInterface $languageService
    )
    {
}

    /**
     * Clear project categories cache for all languages
     */
    private function clearProjectCategoriesCache()
    {
        $languages = $this->languageService->getAllLanguages();
        foreach ($languages as $language) {
            Cache::forget("api:project-categories:all:lang:{$language->id}");
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projectCategories = $this->projectCategoryService->getProjectCategories();
        return view('admin.project-categories.index', compact('projectCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.project-categories.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectCategoryRequest $request)
    {
        $request->validated();

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->imageService->upload($request->file('image'), 'project-categories');
            }
            $projectCategory = ProjectCategory::create([
                'status' => $request->input('status', false),
                'image' => $imagePath,
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    ProjectCategoryTranslate::create([
                        'project_category_id' => $projectCategory->id,
                        'language_id' => $languageId,
                        'title' => $translation['title'],
                        'slug' => $translation['slug'],
                        'seo_title' => $translation['seo_title'] ?? null,
                        'seo_keywords' => $translation['seo_keywords'] ?? null,
                        'seo_description' => $translation['seo_description'] ?? null,
                        'seo_text' => $translation['seo_text'] ?? null,
                        'image_alt_text' => $translation['image_alt_text'] ?? null,
                    ]);
                }
            }

            // Clear cache after creating
            $this->clearProjectCategoriesCache();

            return redirect()->route('admin.project-categories.index')
                ->with('success', __('Project category created successfully.'));

        } catch (\Exception $e) {
            Log::error('Error creating project category: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', __('Error creating project category. Please try again.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectCategory $projectCategory)
    {
        $languages = $this->languageService->getAllLanguages();
        $translations = $projectCategory->translates->keyBy('language_id');
        return view('admin.project-categories.edit', compact('projectCategory', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $request->validated();

        try {
            $data = [
                'status' => $request->input('status', false),
            ];
            if ($request->hasFile('image')) {
                if ($projectCategory->image) {
                    $this->imageService->delete($projectCategory->image);
                }
                $data['image'] = $this->imageService->upload($request->file('image'), 'project-categories');
            }
            $projectCategory->update($data);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    ProjectCategoryTranslate::updateOrCreate(
                        [
                            'project_category_id' => $projectCategory->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'title' => $translation['title'],
                            'slug' => $translation['slug'],
                            'seo_title' => $translation['seo_title'] ?? null,
                            'seo_keywords' => $translation['seo_keywords'] ?? null,
                            'seo_description' => $translation['seo_description'] ?? null,
                            'seo_text' => $translation['seo_text'] ?? null,
                            'image_alt_text' => $translation['image_alt_text'] ?? null,
                        ]
                    );
                }
            }

            // Clear cache after updating
            $this->clearProjectCategoriesCache();

            return redirect()->route('admin.project-categories.index')
                ->with('success', __('Project category updated successfully.'));

        } catch (\Exception $e) {
            Log::error('Error updating project category: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', __('Error updating project category. Please try again.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        try {
            $projectCategory->delete();
            
            // Clear cache after deleting
            $this->clearProjectCategoriesCache();
            
            return redirect()->route('admin.project-categories.index')
                ->with('success', __('Project category deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Error deleting project category: ' . $e->getMessage());
            return back()->with('error', __('Error deleting project category. Please try again.'));
        }
    }

    /**
     * Reorder project categories
     */
    public function reorder(ReorderProjectCategoryRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->items as $index => $id) {
                ProjectCategory::where('id', $id)->update(['order' => $index + 1]);
            }

            // Clear cache after reordering
            $this->clearProjectCategoriesCache();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering project categories: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering project categories'], 500);
        }
    }

    /**
     * Bulk delete project categories
     */
    public function bulkDelete(BulkDeleteProjectCategoryRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = ProjectCategory::whereIn('id', $request->ids)->delete();
            
            // Clear cache after bulk deleting
            $this->clearProjectCategoriesCache();
            
            return redirect()->route('admin.project-categories.index')
                ->with('success', __(':count project category(ies) deleted successfully!', ['count' => $deletedCount]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting project categories: :error', ['error' => $e->getMessage()]));
        }
    }
}
