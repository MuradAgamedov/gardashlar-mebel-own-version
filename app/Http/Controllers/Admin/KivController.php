<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kiv;
use App\Models\KivCategory;
use App\Services\ImageService;
use Illuminate\Http\Request;

class KivController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kivs = Kiv::with('category.translates')->orderBy('order')->get();
        $categories = KivCategory::with('translates')->orderBy('order')->get();
        
        return view('admin.kivs.index', compact('kivs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = KivCategory::with('translates')->orderBy('order')->get();
        return view('admin.kivs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Base validation
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'video_link' => 'nullable|string|max:500',
            'kiv_category_id' => 'required|exists:kiv_categories,id',
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'video_link.max' => 'Video link may not be greater than 500 characters.',
            'kiv_category_id.required' => 'Category is required.',
            'kiv_category_id.exists' => 'Selected category does not exist.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->upload(
                $request->file('image'),
                'kivs',
                [
                    'max_size' => 2048,
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                ]
            );
            
            if (!$imagePath) {
                return redirect()->back()
                    ->with('error', 'Failed to upload image.')
                    ->withInput();
            }
        }

        // Create KIV
        $kiv = Kiv::create([
            'image' => $imagePath,
            'video_link' => $request->input('video_link'),
            'kiv_category_id' => $request->input('kiv_category_id'),
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.kivs.index')
            ->with('success', 'KIV created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kiv $kiv)
    {
        $kiv->load('category.translates');
        $categories = KivCategory::with('translates')->orderBy('order')->get();
        
        return view('admin.kivs.edit', compact('kiv', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kiv $kiv)
    {
        // Base validation
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'video_link' => 'nullable|string|max:500',
            'kiv_category_id' => 'required|exists:kiv_categories,id',
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'video_link.max' => 'Video link may not be greater than 500 characters.',
            'kiv_category_id.required' => 'Category is required.',
            'kiv_category_id.exists' => 'Selected category does not exist.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Handle image upload
        $imageData = [];
        if ($request->hasFile('image')) {
            $uploadedPath = $this->imageService->update(
                $request->file('image'),
                $kiv->image,
                'kivs',
                [
                    'max_size' => 2048,
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                ]
            );
            
            if ($uploadedPath) {
                $imageData['image'] = $uploadedPath;
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to upload image.')
                    ->withInput();
            }
        }

        // Update KIV
        $kiv->update(array_merge($imageData, [
            'video_link' => $request->input('video_link'),
            'kiv_category_id' => $request->input('kiv_category_id'),
            'status' => $request->input('status'),
        ]));

        return redirect()->route('admin.kivs.index')
            ->with('success', 'KIV updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kiv $kiv)
    {
        try {
            $kiv->delete();
            return redirect()->route('admin.kivs.index')
                ->with('success', 'KIV deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting KIV: ' . $e->getMessage());
        }
    }

    /**
     * Reorder KIVs
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'kivs' => 'required|array',
            'kivs.*' => 'required|integer|exists:kivs,id',
        ]);

        try {
            foreach ($request->kivs as $index => $kivId) {
                Kiv::where('id', $kivId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 