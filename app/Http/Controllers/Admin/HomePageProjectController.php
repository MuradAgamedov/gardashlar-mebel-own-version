<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageProject;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\HomePageProjectServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\HomePageProject\StoreHomePageProjectRequest;
use App\Http\Requests\Admin\HomePageProject\UpdateHomePageProjectRequest;

class HomePageProjectController extends Controller
{
    public function __construct(
        protected HomePageProjectServiceInterface $homePageProjectService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $homePageProjects = $this->homePageProjectService->getHomePageProjects();
        return view('admin.home-page-projects.index', compact('homePageProjects'));
    }

    public function create()
    {
        $mainLanguage = $this->languageService->getMainLanguage();
        $projects = $this->homePageProjectService->getProjectsWithMainLanguage($mainLanguage);
        $categories = $this->homePageProjectService->getCategoriesWithMainLanguage($mainLanguage);
        return view('admin.home-page-projects.create', compact('projects', 'categories', 'mainLanguage'));
    }

    public function store(StoreHomePageProjectRequest $request)
    {
        try {
            $this->homePageProjectService->createHomePageProject([
                'project_id' => $request->project_id,
                'project_category_id' => $request->project_category_id,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', __('Project added to home page successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating home page project: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function show(HomePageProject $homePageProject)
    {
        return view('admin.home-page-projects.show', compact('homePageProject'));
    }

    public function edit(HomePageProject $homePageProject)
    {
        $mainLanguage = $this->languageService->getMainLanguage();
        $projects = $this->homePageProjectService->getProjectsWithMainLanguage($mainLanguage);
        $categories = $this->homePageProjectService->getCategoriesWithMainLanguage($mainLanguage);
        return view('admin.home-page-projects.edit', compact('homePageProject', 'projects', 'categories', 'mainLanguage'));
    }

    public function update(UpdateHomePageProjectRequest $request, HomePageProject $homePageProject)
    {
        try {
            $this->homePageProjectService->updateHomePageProject($homePageProject, [
                'project_id' => $request->project_id,
                'project_category_id' => $request->project_category_id,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', __('Home page project updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating home page project: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(HomePageProject $homePageProject)
    {
        try {
            $this->homePageProjectService->deleteHomePageProject($homePageProject);
            return redirect()->route('admin.home-page-projects.index')
                ->with('success', __('Project removed from home page successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting home page project: :error', ['error' => $e->getMessage()]));
        }
    }

    public function getProjectsByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $mainLanguage = $this->languageService->getMainLanguage();
        $projects = $this->homePageProjectService->getProjectsByCategory($categoryId, $mainLanguage);
        return response()->json($projects);
    }

    public function reorder(Request $request)
    {
        try {
            $this->homePageProjectService->reorderHomePageProjects($request->input('items', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
