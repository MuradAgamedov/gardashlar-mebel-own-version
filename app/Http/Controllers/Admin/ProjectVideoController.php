<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProjectVideo\ReorderProjectVideoRequest;

use App\Http\Requests\Admin\ProjectVideo\StoreProjectVideoRequest;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectVideo;
use App\Interfaces\Services\Admin\ProjectVideoServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectVideoController extends Controller
{
    public function __construct(protected ProjectVideoServiceInterface $projectVideoService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $videos = $this->projectVideoService->getVideosByProject($project->id);
        return view('admin.project-videos.index', compact('project', 'videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return view('admin.project-videos.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectVideoRequest $request, Project $project)
    {
        $request->validated();

        try {
            ProjectVideo::create([
                'project_id' => $project->id,
                'video_link' => $request->video_link,
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.projects.videos.index', $project)
                ->with('success', 'Video added successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating project video: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating video. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectVideo $video)
    {
        return view('admin.project-videos.edit', compact('project', 'video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectVideo $video)
    {
        $request->validated();

        try {
            $video->update([
                'video_link' => $request->video_link,
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.projects.videos.index', $project)
                ->with('success', 'Video updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating project video: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating video. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectVideo $video)
    {
        try {
            $video->delete();
            return redirect()->route('admin.projects.videos.index', $project)
                ->with('success', 'Video deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting project video: ' . $e->getMessage());
            return back()->with('error', 'Error deleting video. Please try again.');
        }
    }

    /**
     * Reorder videos
     */
    public function reorder(ReorderProjectVideoRequest $request, Project $project)
    {
        $request->validated();

        try {
            foreach ($request->items as $index => $id) {
                ProjectVideo::where('id', $id)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering project videos: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering videos'], 500);
        }
    }
}