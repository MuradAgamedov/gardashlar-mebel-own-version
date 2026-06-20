<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WhyChooseUs\UpdateWhyChooseUsRequest;

use App\Http\Controllers\Controller;
use App\Models\WhyChooseUs;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\WhyChooseUsServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class WhyChooseUsController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected WhyChooseUsServiceInterface $whyChooseUsService,
        protected LanguageServiceInterface $languageService
    )
    {
}

    /**
     * Show the form for editing the Why Choose Us section.
     */
    public function edit()
    {
        $whyChooseUs = $this->whyChooseUsService->getFirstOrCreate();
        $languages = $this->languageService->getActiveLanguages();
        
        return view('admin.why-choose-us.edit', compact('whyChooseUs', 'languages'));
    }

    /**
     * Update the Why Choose Us section.
     */
    public function update(UpdateWhyChooseUsRequest $request)
    {
        $whyChooseUs = $this->whyChooseUsService->getFirstOrCreate();

        $request->validated();

        try {
            // Handle icon uploads
            $iconData = [];
            
            $iconFields = ['first_icon', 'second_icon', 'third_icon', 'fourth_icon'];
            foreach ($iconFields as $field) {
                if ($request->hasFile($field)) {
                    $uploadedPath = $this->imageService->update(
                        $request->file($field),
                        $whyChooseUs->$field,
                        'why-choose-us/icons',
                        [
                            'max_size' => 2048,
                            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                        ]
                    );
                    
                    if ($uploadedPath) {
                        $iconData[$field] = $uploadedPath;
                    } else {
                        $fieldLabel = str_replace('_', ' ', $field);
                        return redirect()->back()
                            ->with('error', __('Failed to upload :field.', ['field' => __($fieldLabel)]))
                            ->withInput();
                    }
                }
            }

            // Update the main WhyChooseUs record
            if (!empty($iconData)) {
                $whyChooseUs->update($iconData);
            }

            // Delete existing translations and create new ones
            $whyChooseUs->translates()->delete();

            // Create new translations
            foreach ($request->translates as $translate) {
                $whyChooseUs->translates()->create([
                    'language_id' => $translate['language_id'],
                    'first_icon_alt_text' => trim($translate['first_icon_alt_text'] ?? ''),
                    'first_title' => trim($translate['first_title'] ?? ''),
                    'first_description' => trim($translate['first_description'] ?? ''),
                    'second_icon_alt_text' => trim($translate['second_icon_alt_text'] ?? ''),
                    'second_title' => trim($translate['second_title'] ?? ''),
                    'second_description' => trim($translate['second_description'] ?? ''),
                    'third_icon_alt_text' => trim($translate['third_icon_alt_text'] ?? ''),
                    'third_title' => trim($translate['third_title'] ?? ''),
                    'third_description' => trim($translate['third_description'] ?? ''),
                    'fourth_icon_alt_text' => trim($translate['fourth_icon_alt_text'] ?? ''),
                    'fourth_title' => trim($translate['fourth_title'] ?? ''),
                    'fourth_description' => trim($translate['fourth_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.why-choose-us.edit')
                ->with('success', __('Why Choose Us section updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating Why Choose Us section: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
