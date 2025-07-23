<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectImageController extends Controller
{
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads'), $imageName);
            }

            // Get max order
            $maxOrder = $project->images()->max('order') ?? 0;

            $projectImage = $project->images()->create([
                'image' => $imageName,
                'is_main_image' => false, // Always false for new images
                'order' => $maxOrder + 1,
                'status' => $request->boolean('status', true),
            ]);

            return redirect()->route('admin.projects.images.index', $project)
                ->with('success', 'Image added successfully!');

        } catch (\Exception $e) {
            \Log::error('Error creating project image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating image: ' . $e->getMessage())
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
                // Delete old image
                if ($image->image && file_exists(public_path('uploads/' . $image->image))) {
                    unlink(public_path('uploads/' . $image->image));
                }

                $newImage = $request->file('image');
                $imageName = time() . '_' . $newImage->getClientOriginalName();
                $newImage->move(public_path('uploads'), $imageName);
                $data['image'] = $imageName;
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
            // Delete image file
            if ($image->image && file_exists(public_path('uploads/' . $image->image))) {
                unlink(public_path('uploads/' . $image->image));
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
            $items = $request->input('items', []);
            
            foreach ($items as $index => $itemId) {
                $project->images()->where('id', $itemId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
