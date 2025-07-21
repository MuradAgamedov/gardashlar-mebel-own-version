<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhyChooseUs;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;

class WhyChooseUsController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Show the form for editing the Why Choose Us section.
     */
    public function edit()
    {
        $whyChooseUs = WhyChooseUs::getFirstOrCreate();
        $whyChooseUs->load(['translates.language']);
        $languages = Language::active()->orderBy('order')->get();
        
        return view('admin.why-choose-us.edit', compact('whyChooseUs', 'languages'));
    }

    /**
     * Update the Why Choose Us section.
     */
    public function update(Request $request)
    {
        $whyChooseUs = WhyChooseUs::getFirstOrCreate();

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
            'translates.required' => 'At least one language translation is required.',
            'first_icon.image' => 'First icon must be an image file.',
            'first_icon.mimes' => 'First icon must be a file of type: jpg, jpeg, png, gif, webp.',
            'first_icon.max' => 'First icon may not be greater than 2MB.',
            'second_icon.image' => 'Second icon must be an image file.',
            'second_icon.mimes' => 'Second icon must be a file of type: jpg, jpeg, png, gif, webp.',
            'second_icon.max' => 'Second icon may not be greater than 2MB.',
            'third_icon.image' => 'Third icon must be an image file.',
            'third_icon.mimes' => 'Third icon must be a file of type: jpg, jpeg, png, gif, webp.',
            'third_icon.max' => 'Third icon may not be greater than 2MB.',
            'fourth_icon.image' => 'Fourth icon must be an image file.',
            'fourth_icon.mimes' => 'Fourth icon must be a file of type: jpg, jpeg, png, gif, webp.',
            'fourth_icon.max' => 'Fourth icon may not be greater than 2MB.',
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
                        return redirect()->back()
                            ->with('error', 'Failed to upload ' . str_replace('_', ' ', $field) . '.')
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
                ->with('success', 'Why Choose Us section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Why Choose Us section: ' . $e->getMessage())
                ->withInput();
        }
    }
}
