<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTranslate;
use App\Models\ProjectCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Repositories\ProjectRepository;
use App\Repositories\LanguageRepository;


class ProjectController extends Controller
{
    public function __construct(
        public ProjectRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mainLanguage = $this->languageRepository->getMainLanguage();
        $categories = $this->repository->getActiveCategoriesWithMainLanguage($mainLanguage->id);
        $projects = $this->repository->getProjects($request);
        
        return view('admin.projects.index', compact('projects', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        $categories = $this->repository->getActiveCategories();
        return view('admin.projects.create', compact('languages', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_category_id' => 'required|exists:project_categories,id',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.company_name' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255|unique:project_translates,slug',
            'translations.*.description' => 'required|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
        ]);

        try {
            $project = Project::create([
                'project_category_id' => $request->project_category_id,
                'status' => $request->input('status', false),
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    ProjectTranslate::create([
                        'project_id' => $project->id,
                        'language_id' => $languageId,
                        'title' => $translation['title'],
                        'company_name' => $translation['company_name'],
                        'slug' => $translation['slug'],
                        'description' => $translation['description'],
                        'seo_title' => $translation['seo_title'] ?? null,
                        'seo_keywords' => $translation['seo_keywords'] ?? null,
                        'seo_description' => $translation['seo_description'] ?? null,
                    ]);
                }
            }

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()
                ->with('error', 'Error creating project: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $languages = $this->languageRepository->getAllLanguages();
        $categories = $this->repository->getActiveCategories();
        $translations = $project->translates->keyBy('language_id');
        return view('admin.projects.edit', compact('project', 'languages', 'categories', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Custom validation for slug uniqueness excluding current translations
        $customRules = [
            'project_category_id' => 'required|exists:project_categories,id',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.company_name' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
        ];

        // Add slug validation for each translation
        foreach ($request->translations as $languageId => $translation) {
            $existingTranslation = $project->translates()->where('language_id', $languageId)->first();
            $slugRule = 'required|string|max:255';
            
            if ($existingTranslation) {
                $slugRule .= '|unique:project_translates,slug,' . $existingTranslation->id;
            } else {
                $slugRule .= '|unique:project_translates,slug';
            }
            
            $customRules["translations.{$languageId}.slug"] = $slugRule;
        }

        $request->validate($customRules);

        try {
            $project->update([
                'project_category_id' => $request->project_category_id,
                'status' => $request->input('status', false),
            ]);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    $existingTranslation = $project->translates()->where('language_id', $languageId)->first();
                    
                    if ($existingTranslation) {
                        // Update existing translation
                        $existingTranslation->update([
                            'title' => $translation['title'],
                            'company_name' => $translation['company_name'],
                            'slug' => $translation['slug'],
                            'description' => $translation['description'],
                            'seo_title' => $translation['seo_title'] ?? null,
                            'seo_keywords' => $translation['seo_keywords'] ?? null,
                            'seo_description' => $translation['seo_description'] ?? null,
                        ]);
                    } else {
                        // Create new translation
                        ProjectTranslate::create([
                            'project_id' => $project->id,
                            'language_id' => $languageId,
                            'title' => $translation['title'],
                            'company_name' => $translation['company_name'],
                            'slug' => $translation['slug'],
                            'description' => $translation['description'],
                            'seo_title' => $translation['seo_title'] ?? null,
                            'seo_keywords' => $translation['seo_keywords'] ?? null,
                            'seo_description' => $translation['seo_description'] ?? null,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating project: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating project. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('admin.projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting project: ' . $e->getMessage());
            return back()->with('error', 'Error deleting project. Please try again.');
        }
    }

    /**
     * Reorder projects
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:projects,id'
        ]);

        try {
            foreach ($request->items as $index => $id) {
                $this->repository->updateOrder($id, $index + 1);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering projects: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering projects'], 500);
        }
    }

    /**
     * Bulk delete projects
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:projects,id',
        ]);

        try {
            $deletedCount = $this->repository->bulkDelete($request->ids);
            
            return redirect()->route('admin.projects.index')
                ->with('success', "{$deletedCount} project(s) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting projects: ' . $e->getMessage());
        }
    }
}
