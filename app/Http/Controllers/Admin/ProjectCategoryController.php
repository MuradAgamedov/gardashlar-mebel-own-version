<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\ImageService;
use App\Repositories\ProjectCategoryRepository;
use App\Repositories\LanguageRepository;

class ProjectCategoryController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService,
        public ProjectCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
        $this->imageService = $imageService;
    }

    /**
     * Clear project categories cache for all languages
     */
    private function clearProjectCategoriesCache()
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            Cache::forget("api:project-categories:all:lang:{$language->id}");
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projectCategories = $this->repository->getProjectCategories();
        return view('admin.project-categories.index', compact('projectCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.project-categories.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'translations.*.seo_text' => 'nullable|string',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

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
                ->with('success', 'Project category created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating project category: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating project category. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectCategory $projectCategory)
    {
        $languages = $this->languageRepository->getAllLanguages();
        $translations = $projectCategory->translates->keyBy('language_id');
        return view('admin.project-categories.edit', compact('projectCategory', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $request->validate([
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'translations.*.seo_text' => 'nullable|string',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

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
                ->with('success', 'Project category updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating project category: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating project category. Please try again.');
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
                ->with('success', 'Project category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting project category: ' . $e->getMessage());
            return back()->with('error', 'Error deleting project category. Please try again.');
        }
    }

    /**
     * Reorder project categories
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:project_categories,id'
        ]);

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
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:project_categories,id',
        ]);

        try {
            $deletedCount = ProjectCategory::whereIn('id', $request->ids)->delete();
            
            // Clear cache after bulk deleting
            $this->clearProjectCategoriesCache();
            
            return redirect()->route('admin.project-categories.index')
                ->with('success', "{$deletedCount} project category(ies) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting project categories: ' . $e->getMessage());
        }
    }
}
