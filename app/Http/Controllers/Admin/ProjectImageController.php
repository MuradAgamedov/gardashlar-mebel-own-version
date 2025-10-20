<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Repositories\ProjectImageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectImageController extends Controller
{
    protected $repository;
     
    public function __construct(ProjectImageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return view('admin.project-images.create', compact('project'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $images = $this->repository->getOrderedImages($project->id);
        return view('admin.project-images.index', compact('project', 'images'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        try {
            $uploadedImages = [];
            $errors = [];

            if ($request->hasFile('images')) {
                $uploadedImages = $this->repository->createImages(
                    $project->id,
                    $request->file('images'),
                    $request->boolean('status', true)
                );
            }

            if (count($uploadedImages) > 0) {
                $message = count($uploadedImages) . ' image(s) uploaded successfully!';
                if (count($errors) > 0) {
                    $message .= ' However, ' . count($errors) . ' image(s) failed to upload.';
                }
                
                return redirect()->route('admin.projects.images.index', $project)
                    ->with('success', $message)
                    ->with('upload_errors', $errors);
            } else {
                return redirect()->back()
                    ->with('error', 'No images were uploaded successfully.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Error creating project images: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating images: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectImage $image)
    {
        return view('admin.project-images.edit', compact('project', 'image'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectImage $image)
    {
        try {
            $data = [
                'status' => $request->boolean('status', true),
            ];

            $newImage = $request->hasFile('image') ? $request->file('image') : null;
            $this->repository->updateImage($image, $data, $newImage);

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Image updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating project image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating image: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectImage $image)
    {
        try {
            $this->repository->deleteImage($image);

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Image deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting project image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    /**
     * Set image as main image
     */
    public function setMainImage(Project $project, ProjectImage $image)
    {
        try {
            $this->repository->setMainImage($project->id, $image->id);

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Main image updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error setting main image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error setting main image: ' . $e->getMessage());
        }
    }

    /**
     * Reorder images
     */
    public function reorder(Request $request, Project $project)
    {
        try {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*' => 'required|integer|exists:project_images,id'
            ]);

            $success = $this->repository->reorderImages($project->id, $request->input('items', []));

            if (!$success) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Some images do not belong to this project'
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering project images: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error reordering images: ' . $e->getMessage()
            ]);
        }
    }
}
