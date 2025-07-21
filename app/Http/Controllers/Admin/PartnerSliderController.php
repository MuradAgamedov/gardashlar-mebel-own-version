<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerSlider;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PartnerSliderController extends Controller
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
        $sliders = PartnerSlider::with('translates.language')->orderBy('order')->get();
        $languages = Language::all();
        
        return view('admin.partner-sliders.index', compact('sliders', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.partner-sliders.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->imageService->upload(
                    $request->file('image'),
                    'partner-sliders',
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

            // Create slider
            $slider = PartnerSlider::create([
                'image' => $imagePath,
                'status' => $request->input('status'),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $slider->translates()->create([
                    'language_id' => $language->id,
                    'title' => trim($translationData['title'] ?? ''),
                ]);
            }

            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating partner slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PartnerSlider $partnerSlider)
    {
        $partnerSlider->load('translates.language');
        $languages = Language::all();
        
        return view('admin.partner-sliders.edit', compact('partnerSlider', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartnerSlider $partnerSlider)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        try {
            // Handle image upload
            $imageData = [];
            if ($request->hasFile('image')) {
                $uploadedPath = $this->imageService->update(
                    $request->file('image'),
                    $partnerSlider->image,
                    'partner-sliders',
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

            // Update slider
            $partnerSlider->update(array_merge($imageData, [
                'status' => $request->input('status'),
            ]));

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $partnerSlider->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating partner slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartnerSlider $partnerSlider)
    {
        try {
            $partnerSlider->delete();
            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting partner slider: ' . $e->getMessage());
        }
    }

    /**
     * Reorder sliders
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'sliders' => 'required|array',
            'sliders.*' => 'required|integer|exists:partner_sliders,id',
        ]);

        try {
            foreach ($request->sliders as $index => $sliderId) {
                PartnerSlider::where('id', $sliderId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
