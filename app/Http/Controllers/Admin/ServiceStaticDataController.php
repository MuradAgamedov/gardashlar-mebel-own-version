<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ServiceStaticData\UpdateServiceStaticDataRequest;

use App\Http\Controllers\Controller;
use App\Models\ServiceStaticData;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\ServiceStaticDataServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class ServiceStaticDataController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected ServiceStaticDataServiceInterface $serviceStaticDataService,
        protected LanguageServiceInterface $languageService
    )
    {
}

    /**
     * Show the form for editing the service static data section.
     */
    public function edit()
    {
        $serviceStaticData = $this->serviceStaticDataService->getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.service-static-data.edit', compact('serviceStaticData', 'languages'));
    }

    /**
     * Update the service static data section.
     */
    public function update(UpdateServiceStaticDataUpdateServiceStaticDataRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();
        
        // Debug: Check what data is being sent
        \Log::info('ServiceStaticData Update Request:', $request->all());
        
        // Base validation for image uploads
        $request->validated();

        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
        }

        try {
            $serviceStaticData = $this->serviceStaticDataService->getFirstOrCreate();

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
                            ->with('error', __('Failed to upload :field.', ['field' => $imageField]))
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
                ->with('success', __('Service Static Data updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating Service Static Data: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
} 