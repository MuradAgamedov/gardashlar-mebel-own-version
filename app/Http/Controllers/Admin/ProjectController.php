<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Project\BulkDeleteProjectRequest;

use App\Http\Requests\Admin\Project\ReorderProjectRequest;

use App\Http\Requests\Admin\Project\UpdateProjectRequest;

use App\Http\Requests\Admin\Project\StoreProjectRequest;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTranslate;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Interfaces\Services\Admin\ProjectServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;


class ProjectController extends Controller
{
    public function __construct(
        protected ProjectServiceInterface $projectService,
        protected LanguageServiceInterface $languageService
    )
    {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mainLanguage = $this->languageService->getMainLanguage();
        $categories = $this->projectService->getActiveCategoriesWithMainLanguage($mainLanguage->id);
        $projects = $this->projectService->getProjects($request);

        return view('admin.projects.index', compact('projects', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        $categories = $this->projectService->getActiveCategories();
        return view('admin.projects.create', compact('languages', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $request->validated();

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
                        'status' => $request->boolean('status'),
                    ]);
                }
            }

            return redirect()->route('admin.projects.index')
                ->with('success', __('Project created successfully.'));
        } catch (\Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withInput()
                ->with('error', __('Error creating project: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $languages = $this->languageService->getAllLanguages();
        $categories = $this->projectService->getActiveCategories();
        $translations = $project->translates->keyBy('language_id');
        return view('admin.projects.edit', compact('project', 'languages', 'categories', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
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

        $request->validated();

        try {
            $project->update([
                'project_category_id' => $request->project_category_id,
                'status' => $request->boolean('status'),
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
                ->with('success', __('Project updated successfully.'));
        } catch (\Exception $e) {
            Log::error('Error updating project: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', __('Error updating project. Please try again.'));
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
                ->with('success', __('Project deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Error deleting project: ' . $e->getMessage());
            return back()->with('error', __('Error deleting project. Please try again.'));
        }
    }

    /**
     * Reorder projects
     */
    public function reorder(ReorderProjectRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->items as $index => $id) {
                Project::where('id', $id)->update(['order' => $index + 1]);
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
    public function bulkDelete(BulkDeleteProjectRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = Project::whereIn('id', $request->ids)->delete();

            return redirect()->route('admin.projects.index')
                ->with('success', __(':count project(s) deleted successfully!', ['count' => $deletedCount]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting projects: :error', ['error' => $e->getMessage()]));
        }
    }
}
