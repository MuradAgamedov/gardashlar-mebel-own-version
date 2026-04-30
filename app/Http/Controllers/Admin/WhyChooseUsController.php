<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhyChooseUs;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\WhyChooseUsRepository;
use App\Repositories\LanguageRepository;

class WhyChooseUsController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService,
        public WhyChooseUsRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
        $this->imageService = $imageService;
    }

    /**
     * Show the form for editing the Why Choose Us section.
     */
    public function edit()
    {
        $whyChooseUs = $this->repository->getFirstOrCreate();
        $languages = $this->languageRepository->getActiveLanguages();
        
        return view('admin.why-choose-us.edit', compact('whyChooseUs', 'languages'));
    }

    /**
     * Update the Why Choose Us section.
     */
    public function update(Request $request)
    {
        $whyChooseUs = $this->repository->getFirstOrCreate();

        $request->validate([
            'first_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'second_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'third_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'fourth_icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.first_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.first_title' => 'nullable|string|max:255',
            'translates.*.first_description' => 'nullable|string',
            'translates.*.second_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.second_title' => 'nullable|string|max:255',
            'translates.*.second_description' => 'nullable|string',
            'translates.*.third_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.third_title' => 'nullable|string|max:255',
            'translates.*.third_description' => 'nullable|string',
            'translates.*.fourth_icon_alt_text' => 'nullable|string|max:255',
            'translates.*.fourth_title' => 'nullable|string|max:255',
            'translates.*.fourth_description' => 'nullable|string',
        ], [
            'translates.required' => __('At least one language translation is required.'),
            'first_icon.image' => __('First icon must be an image file.'),
            'first_icon.mimes' => __('First icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'first_icon.max' => __('First icon may not be greater than :size.', ['size' => '2MB']),
            'second_icon.image' => __('Second icon must be an image file.'),
            'second_icon.mimes' => __('Second icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'second_icon.max' => __('Second icon may not be greater than :size.', ['size' => '2MB']),
            'third_icon.image' => __('Third icon must be an image file.'),
            'third_icon.mimes' => __('Third icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'third_icon.max' => __('Third icon may not be greater than :size.', ['size' => '2MB']),
            'fourth_icon.image' => __('Fourth icon must be an image file.'),
            'fourth_icon.mimes' => __('Fourth icon must be a file of type: :types.', ['types' => 'jpg, jpeg, png, gif, webp']),
            'fourth_icon.max' => __('Fourth icon may not be greater than :size.', ['size' => '2MB']),
        ]);

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
