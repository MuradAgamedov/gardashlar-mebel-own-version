<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SimpleImage;
use App\Models\SimpleImageTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\ImageService;

class SimpleImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $images = SimpleImage::with('translates.language')->get();
        return view('admin.simple-images.index', compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.simple-images.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:simple_images',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.image_go_url' => 'nullable|string|max:255',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $imagePath = $this->imageService->upload($request->file('image'), 'simple-images');
            
            if (!$imagePath) {
                return back()->withInput()
                    ->with('error', 'Error uploading image. Please try again.');
            }

            $simpleImage = SimpleImage::create([
                'keyword' => $request->keyword,
                'image_path' => $imagePath,
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                SimpleImageTranslate::create([
                    'simple_image_id' => $simpleImage->id,
                    'language_id' => $languageId,
                    'image_go_url' => $translation['image_go_url'] ?? null,
                    'image_alt_text' => $translation['image_alt_text'] ?? null,
                ]);
            }

            return redirect()->route('admin.simple-images.index')
                ->with('success', 'Image created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating image. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SimpleImage $simpleImage): View
    {
        $languages = Language::active()->orderBy('order')->get();
        $translations = $simpleImage->translates->keyBy('language_id');
        return view('admin.simple-images.edit', compact('simpleImage', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SimpleImage $simpleImage): RedirectResponse
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:simple_images,keyword,' . $simpleImage->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.image_go_url' => 'nullable|string|max:255',
            'translations.*.image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $data = [
                'keyword' => $request->keyword,
            ];

            if ($request->hasFile('image')) {
                // Delete old image
                if ($simpleImage->image_path) {
                    $this->imageService->delete($simpleImage->image_path);
                }
                
                // Upload new image
                $imagePath = $this->imageService->upload($request->file('image'), 'simple-images');
                
                if (!$imagePath) {
                    return back()->withInput()
                        ->with('error', 'Error uploading image. Please try again.');
                }
                
                $data['image_path'] = $imagePath;
            }

            $simpleImage->update($data);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                SimpleImageTranslate::updateOrCreate(
                    [
                        'simple_image_id' => $simpleImage->id,
                        'language_id' => $languageId,
                    ],
                    [
                        'image_go_url' => $translation['image_go_url'] ?? null,
                        'image_alt_text' => $translation['image_alt_text'] ?? null,
                    ]
                );
            }

            return redirect()->route('admin.simple-images.index')
                ->with('success', 'Image updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating image. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SimpleImage $simpleImage): RedirectResponse
    {
        try {
            // Delete image file
            if ($simpleImage->image_path) {
                $this->imageService->delete($simpleImage->image_path);
            }
            
            $simpleImage->delete();
            
            return redirect()->route('admin.simple-images.index')
                ->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image. Please try again.');
        }
    }
}
