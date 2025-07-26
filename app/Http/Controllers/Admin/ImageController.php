<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\ImageTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ImageService;

class ImageController extends Controller
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
        $images = Image::with('translates.language')->get();
        return view('admin.images.index', compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.images.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.image_alt_text' => 'required|string|max:255',
        ]);

        try {
            $imagePath = $this->imageService->upload($request->file('image'), 'images');
            
            $image = Image::create([
                'keyword' => $request->input('keyword'),
                'image' => $imagePath,
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['image_alt_text'])) {
                    ImageTranslate::create([
                        'image_id' => $image->id,
                        'language_id' => $languageId,
                        'image_alt_text' => $translation['image_alt_text'],
                    ]);
                }
            }

            return redirect()->route('admin.images.index')
                ->with('success', 'Image created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating image: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating image. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        $languages = Language::all();
        $translations = $image->translates->keyBy('language_id');
        return view('admin.images.edit', compact('image', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.image_alt_text' => 'required|string|max:255',
        ]);

        try {
            $data = [
                'keyword' => $request->input('keyword'),
            ];

            if ($request->hasFile('image')) {
                if ($image->image) {
                    $this->imageService->delete($image->image);
                }
                $data['image'] = $this->imageService->upload($request->file('image'), 'images');
            }

            $image->update($data);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['image_alt_text'])) {
                    ImageTranslate::updateOrCreate(
                        [
                            'image_id' => $image->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'image_alt_text' => $translation['image_alt_text'],
                        ]
                    );
                }
            }

            return redirect()->route('admin.images.index')
                ->with('success', 'Image updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating image: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating image. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        try {
            if ($image->image) {
                $this->imageService->delete($image->image);
            }
            $image->delete();
            return redirect()->route('admin.images.index')
                ->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
            return back()->with('error', 'Error deleting image. Please try again.');
        }
    }
}
