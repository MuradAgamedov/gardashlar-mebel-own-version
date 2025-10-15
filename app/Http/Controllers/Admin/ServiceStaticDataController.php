<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceStaticData;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\ServiceStaticDataRepository;
use App\Repositories\LanguageRepository;

class ServiceStaticDataController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService,
        public ServiceStaticDataRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
        $this->imageService = $imageService;
    }

    /**
     * Show the form for editing the service static data section.
     */
    public function edit()
    {
        $serviceStaticData = $this->repository->getFirstOrCreate();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.service-static-data.edit', compact('serviceStaticData', 'languages'));
    }

    /**
     * Update the service static data section.
     */
    public function update(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Debug: Check what data is being sent
        \Log::info('ServiceStaticData Update Request:', $request->all());
        
        // Base validation for image uploads
        $request->validate([
            'card_1_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'card_2_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'card_3_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
        ], [
            'card_1_image.image' => 'Card 1 image must be an image file.',
            'card_1_image.mimes' => 'Card 1 image must be a file of type: jpg, jpeg, png, gif, webp, svg.',
            'card_1_image.max' => 'Card 1 image may not be greater than 2MB.',
            'card_2_image.image' => 'Card 2 image must be an image file.',
            'card_2_image.mimes' => 'Card 2 image must be a file of type: jpg, jpeg, png, gif, webp, svg.',
            'card_2_image.max' => 'Card 2 image may not be greater than 2MB.',
            'card_3_image.image' => 'Card 3 image must be an image file.',
            'card_3_image.mimes' => 'Card 3 image must be a file of type: jpg, jpeg, png, gif, webp, svg.',
            'card_3_image.max' => 'Card 3 image may not be greater than 2MB.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'nullable|string|max:255',
                "translations.{$language->id}.description" => 'nullable|string|max:2000',
                "translations.{$language->id}.card_1_image_title" => 'nullable|string|max:255',
                "translations.{$language->id}.card_1_image_alt_text" => 'nullable|string|max:255',
                "translations.{$language->id}.card_2_image_title" => 'nullable|string|max:255',
                "translations.{$language->id}.card_2_image_alt_text" => 'nullable|string|max:255',
                "translations.{$language->id}.card_3_image_title" => 'nullable|string|max:255',
                "translations.{$language->id}.card_3_image_alt_text" => 'nullable|string|max:255',
            ], [
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.max" => "Description may not be greater than 2000 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_1_image_title.max" => "Card 1 image title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_1_image_alt_text.max" => "Card 1 image alt text may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_2_image_title.max" => "Card 2 image title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_2_image_alt_text.max" => "Card 2 image alt text may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_3_image_title.max" => "Card 3 image title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.card_3_image_alt_text.max" => "Card 3 image alt text may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        try {
            $serviceStaticData = $this->repository->getFirstOrCreate();

            // Handle image uploads
            $imageData = [];
            $imageFields = ['card_1_image', 'card_2_image', 'card_3_image'];
            
            foreach ($imageFields as $imageField) {
                if ($request->hasFile($imageField)) {
                    $uploadedPath = $this->imageService->update(
                        $request->file($imageField),
                        $serviceStaticData->$imageField,
                        'service-static-data',
                        [
                            'max_size' => 2048,
                            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                        ]
                    );
                    
                    if ($uploadedPath) {
                        $imageData[$imageField] = $uploadedPath;
                    } else {
                        return redirect()->back()
                            ->with('error', "Failed to upload {$imageField}.")
                            ->withInput();
                    }
                }
            }

            // Update service static data
            if (!empty($imageData)) {
                $serviceStaticData->update($imageData);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $serviceStaticData->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                        'description' => trim($translationData['description'] ?? ''),
                        'card_1_image_title' => trim($translationData['card_1_image_title'] ?? ''),
                        'card_1_image_alt_text' => trim($translationData['card_1_image_alt_text'] ?? ''),
                        'card_2_image_title' => trim($translationData['card_2_image_title'] ?? ''),
                        'card_2_image_alt_text' => trim($translationData['card_2_image_alt_text'] ?? ''),
                        'card_3_image_title' => trim($translationData['card_3_image_title'] ?? ''),
                        'card_3_image_alt_text' => trim($translationData['card_3_image_alt_text'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.service-static-data.edit')
                ->with('success', 'Service Static Data updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Service Static Data: ' . $e->getMessage())
                ->withInput();
        }
    }
} 