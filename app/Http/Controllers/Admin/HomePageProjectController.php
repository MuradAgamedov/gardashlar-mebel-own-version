<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageProject;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homePageProjects = HomePageProject::with(['project.translates.language', 'projectCategory.translates.language'])
            ->orderBy('order')
            ->get();

        return view('admin.home-page-projects.index', compact('homePageProjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mainLanguage = Language::getMainLanguage();
        $projects = Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();
        
        $categories = ProjectCategory::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();

        return view('admin.home-page-projects.create', compact('projects', 'categories', 'mainLanguage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id|unique:home_page_projects,project_id',
            'project_category_id' => 'required|exists:project_categories,id',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get max order
            $maxOrder = HomePageProject::max('order') ?? 0;

            HomePageProject::create([
                'project_id' => $request->project_id,
                'project_category_id' => $request->project_category_id,
                'order' => $maxOrder + 1,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', 'Project added to home page successfully!');

        } catch (\Exception $e) {
            \Log::error('Error creating home page project: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating home page project: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HomePageProject $homePageProject)
    {
        return view('admin.home-page-projects.show', compact('homePageProject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomePageProject $homePageProject)
    {
        $mainLanguage = Language::getMainLanguage();
        $projects = Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();
        
        $categories = ProjectCategory::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();

        return view('admin.home-page-projects.edit', compact('homePageProject', 'projects', 'categories', 'mainLanguage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomePageProject $homePageProject)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id|unique:home_page_projects,project_id,' . $homePageProject->id,
            'project_category_id' => 'required|exists:project_categories,id',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $homePageProject->update([
                'project_id' => $request->project_id,
                'project_category_id' => $request->project_category_id,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', 'Home page project updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error updating home page project: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating home page project: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomePageProject $homePageProject)
    {
        try {
            $homePageProject->delete();

            return redirect()->route('admin.home-page-projects.index')
                ->with('success', 'Project removed from home page successfully!');

        } catch (\Exception $e) {
            \Log::error('Error deleting home page project: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting home page project: ' . $e->getMessage());
        }
    }

    /**
     * Get projects by category via AJAX
     */
    public function getProjectsByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $mainLanguage = Language::getMainLanguage();
        
        $projects = Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])
        ->where('project_category_id', $categoryId)
        ->get();
        
        return response()->json($projects);
    }

    /**
     * Reorder projects
     */
    public function reorder(Request $request)
    {
        try {
            $items = $request->input('items', []);
            
            foreach ($items as $index => $itemId) {
                HomePageProject::where('id', $itemId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
