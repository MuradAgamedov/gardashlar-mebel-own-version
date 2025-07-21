<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;

class CertificateController extends Controller
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
        $certificates = Certificate::with('translates.language')->orderBy('order')->get();
        $languages = Language::all();
        
        return view('admin.certificates.index', compact('certificates', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.certificates.create', compact('languages'));
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
            'date' => 'required|string|max:255',
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'date.required' => 'Date is required.',
            'date.max' => 'Date may not be greater than 255 characters.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.image_alt_text" => 'required|string|max:255',
                "translations.{$language->id}.title" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.image_alt_text.required" => "Image alt text is required for {$language->lang_code}.",
                "translations.{$language->id}.image_alt_text.max" => "Image alt text may not be greater than 255 characters for {$language->lang_code}.",
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
                    'certificates',
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

            // Create certificate
            $certificate = Certificate::create([
                'image' => $imagePath,
                'date' => $request->input('date'),
                'status' => $request->input('status'),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $certificate->translates()->create([
                    'language_id' => $language->id,
                    'image_alt_text' => trim($translationData['image_alt_text'] ?? ''),
                    'title' => trim($translationData['title'] ?? ''),
                ]);
            }

            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificate created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating certificate: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        $certificate->load('translates.language');
        $languages = Language::all();
        
        return view('admin.certificates.edit', compact('certificate', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'date' => 'required|string|max:255',
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'date.required' => 'Date is required.',
            'date.max' => 'Date may not be greater than 255 characters.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.image_alt_text" => 'required|string|max:255',
                "translations.{$language->id}.title" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.image_alt_text.required" => "Image alt text is required for {$language->lang_code}.",
                "translations.{$language->id}.image_alt_text.max" => "Image alt text may not be greater than 255 characters for {$language->lang_code}.",
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
                    $certificate->image,
                    'certificates',
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

            // Update certificate
            $certificate->update(array_merge($imageData, [
                'date' => $request->input('date'),
                'status' => $request->input('status'),
            ]));

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $certificate->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'image_alt_text' => trim($translationData['image_alt_text'] ?? ''),
                        'title' => trim($translationData['title'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificate updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating certificate: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        try {
            $certificate->delete();
            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificate deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting certificate: ' . $e->getMessage());
        }
    }

    /**
     * Reorder certificates
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'certificates' => 'required|array',
            'certificates.*' => 'required|integer|exists:certificates,id',
        ]);

        try {
            foreach ($request->certificates as $index => $certificateId) {
                Certificate::where('id', $certificateId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 