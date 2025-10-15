<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageProject;
use Illuminate\Http\Request;
use App\Repositories\HomePageProjectRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\HomePageProject\StoreHomePageProjectRequest;
use App\Http\Requests\Admin\HomePageProject\UpdateHomePageProjectRequest;

class HomePageProjectController extends Controller
{
    public function __construct(
        public HomePageProjectRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $homePageProjects = $this->repository->getHomePageProjects();
        return view('admin.home-page-projects.index', compact('homePageProjects'));
    }

    public function create()
    {
        $mainLanguage = $this->languageRepository->getMainLanguage();
        $projects = $this->repository->getProjectsWithMainLanguage($mainLanguage);
        $categories = $this->repository->getCategoriesWithMainLanguage($mainLanguage);
        return view('admin.home-page-projects.create', compact('projects', 'categories', 'mainLanguage'));
    }

    public function store(StoreHomePageProjectRequest $request)
    {
        try {
            $this->repository->createHomePageProject([
                'project_id' => $request->project_id,
                'project_category_id' => $request->project_category_id,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', 'Project added to home page successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating home page project: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(HomePageProject $homePageProject)
    {
        return view('admin.home-page-projects.show', compact('homePageProject'));
    }

    public function edit(HomePageProject $homePageProject)
    {
        $mainLanguage = $this->languageRepository->getMainLanguage();
        $projects = $this->repository->getProjectsWithMainLanguage($mainLanguage);
        $categories = $this->repository->getCategoriesWithMainLanguage($mainLanguage);
        return view('admin.home-page-projects.edit', compact('homePageProject', 'projects', 'categories', 'mainLanguage'));
    }

    public function update(UpdateHomePageProjectRequest $request, HomePageProject $homePageProject)
    {
        try {
            $this->repository->updateHomePageProject($homePageProject, [
                'project_id' => $request->project_id,
                'project_category_id' => $request->project_category_id,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', 'Home page project updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating home page project: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(HomePageProject $homePageProject)
    {
        try {
            $this->repository->deleteHomePageProject($homePageProject);
            return redirect()->route('admin.home-page-projects.index')
                ->with('success', 'Project removed from home page successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting home page project: ' . $e->getMessage());
        }
    }

    public function getProjectsByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $mainLanguage = $this->languageRepository->getMainLanguage();
        $projects = $this->repository->getProjectsByCategory($categoryId, $mainLanguage);
        return response()->json($projects);
    }

    public function reorder(Request $request)
    {
        try {
            $this->repository->reorderHomePageProjects($request->input('items', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
