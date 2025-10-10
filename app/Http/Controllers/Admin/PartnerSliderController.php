<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerSlider;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\PartnerSliderRepository;
use App\Repositories\LanguageRepository;
use Illuminate\Support\Facades\DB;

class PartnerSliderController extends Controller
{
    protected $imageService;
    protected $repository;
    protected $languageRepository;

    public function __construct(
        ImageService $imageService,
        PartnerSliderRepository $repository,
        LanguageRepository $languageRepository
    ) {
        $this->imageService = $imageService;
        $this->repository = $repository;
        $this->languageRepository = $languageRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = $this->repository->getPartnerSliders();
        $languages = $this->languageRepository->getAllLanguages();

        return view('admin.partner-sliders.index', compact('sliders', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.partner-sliders.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();

        // Validate main fields
        $request->validate([
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'nullable|boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be of type: jpg, jpeg, png, gif, webp.',
            'image.max'   => 'Image size cannot exceed 2MB.',
        ]);

        // Validate translations
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title cannot exceed 255 characters for {$language->lang_code}.",
            ]);
        }

        DB::beginTransaction();
        try {
            // Upload image if exists
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->imageService->upload(
                    $request->file('image'),
                    'partner-sliders',
                    [
                        'max_size'       => 2048,
                        'allowed_types'  => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );

                if (!$imagePath) {
                    throw new \Exception('Failed to upload image.');
                }
            }

            // Create PartnerSlider
            $slider = PartnerSlider::create([
                'image'  => $imagePath,
                'status' => $request->boolean('status'),
            ]);

            // Save translations
            foreach ($languages as $language) {
                $translation = $request->input("translations.{$language->id}");
                $slider->translates()->create([
                    'language_id' => $language->id,
                    'title'       => trim($translation['title'] ?? ''),
                ]);
            }

            DB::commit();
            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider created successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
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

        // Validate main fields
        $request->validate([
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'nullable|boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be of type: jpg, jpeg, png, gif, webp.',
            'image.max'   => 'Image size cannot exceed 2MB.',
        ]);

        // Validate translations
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title cannot exceed 255 characters for {$language->lang_code}.",
            ]);
        }

        DB::beginTransaction();
        try {
            // Handle image update
            $updateData = [
                'status' => $request->boolean('status'),
            ];

            if ($request->hasFile('image')) {
                $uploaded = $this->imageService->update(
                    $request->file('image'),
                    $partnerSlider->image,
                    'partner-sliders',
                    [
                        'max_size'       => 2048,
                        'allowed_types'  => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );

                if (!$uploaded) {
                    throw new \Exception('Failed to upload image.');
                }

                $updateData['image'] = $uploaded;
            }

            $partnerSlider->update($updateData);

            // Update translations
            foreach ($languages as $language) {
                $translation = $request->input("translations.{$language->id}");
                $partnerSlider->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    ['title' => trim($translation['title'] ?? '')]
                );
            }

            DB::commit();
            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider updated successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
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
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error deleting partner slider: ' . $e->getMessage());
        }
    }

    /**
     * Reorder sliders (for drag & drop sorting)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'sliders'   => 'required|array',
            'sliders.*' => 'required|integer|exists:partner_sliders,id',
        ]);

        try {
            foreach ($request->sliders as $index => $sliderId) {
                PartnerSlider::where('id', $sliderId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
