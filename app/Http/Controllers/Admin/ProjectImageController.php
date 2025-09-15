<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageService;

class ProjectImageController extends Controller
{
    protected $imageService;
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $images = $project->images()->orderBy('order')->get();
        return view('admin.project-images.index', compact('project', 'images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return view('admin.project-images.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $uploadedImages = [];
            $errors = [];

            // Get max order
            $maxOrder = $project->images()->max('order') ?? 0;

            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    try {
                        $imagePath = $this->imageService->upload($image, 'projects');
                        
                        $projectImage = $project->images()->create([
                            'image' => $imagePath,
                            'is_main_image' => false, // Always false for new images
                            'order' => $maxOrder + $index + 1,
                            'status' => $request->boolean('status', true),
                        ]);

                        $uploadedImages[] = $projectImage;
                    } catch (\Exception $e) {
                        $errors[] = "Error uploading image " . ($index + 1) . ": " . $e->getMessage();
                        \Log::error('Error uploading project image ' . ($index + 1) . ': ' . $e->getMessage());
                    }
                }
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
                    ->with('error', 'No images were uploaded successfully. ' . implode(' ', $errors))
                    ->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Error creating project images: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating images: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, ProjectImage $image)
    {
        return view('admin.project-images.show', compact('project', 'image'));
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
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'status' => $request->boolean('status', true),
            ];

            // Handle image upload if new image is provided
            if ($request->hasFile('image')) {
                if ($image->image) {
                    $this->imageService->delete($image->image);
                }
                $imagePath = $this->imageService->upload($request->file('image'), 'projects');
                $data['image'] = $imagePath;
            }



            $image->update($data);

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Image updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error updating project image: ' . $e->getMessage());
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
            if ($image->image) {
                $this->imageService->delete($image->image);
            }

            $image->delete();

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Image deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Error deleting project image: ' . $e->getMessage());
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
            // Unmark all other images as main
            $project->images()->update(['is_main_image' => false]);
            
            // Mark this image as main
            $image->update(['is_main_image' => true]);

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Main image updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error setting main image: ' . $e->getMessage());
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

            $items = $request->input('items', []);
            
            // Verify all images belong to this project
            $projectImageIds = $project->images()->pluck('id')->toArray();
            $validItems = array_intersect($items, $projectImageIds);
            
            if (count($validItems) !== count($items)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Some images do not belong to this project'
                ]);
            }
            
            foreach ($validItems as $index => $itemId) {
                $project->images()->where('id', $itemId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error reordering project images: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error reordering images: ' . $e->getMessage()
            ]);
        }
    }
}
