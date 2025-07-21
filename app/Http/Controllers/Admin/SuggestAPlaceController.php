<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlace;
use App\Models\Language;
use Illuminate\Http\Request;

class SuggestAPlaceController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $suggestAPlace = SuggestAPlace::getFirstOrCreate();
        $suggestAPlace->load('translates.language');
        $languages = Language::all();
        
        return view('admin.suggest-a-place.edit', compact('suggestAPlace', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $suggestAPlace = SuggestAPlace::getFirstOrCreate();
        $languages = Language::all();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string',
                "translations.{$language->id}.second_title" => 'required|string|max:255',
                "translations.{$language->id}.second_title_subtitle" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
                "translations.{$language->id}.second_title.required" => "Second title is required for {$language->lang_code}.",
                "translations.{$language->id}.second_title.max" => "Second title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.second_title_subtitle.required" => "Second title subtitle is required for {$language->lang_code}.",
                "translations.{$language->id}.second_title_subtitle.max" => "Second title subtitle may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        try {
            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $suggestAPlace->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                        'description' => trim($translationData['description'] ?? ''),
                        'second_title' => trim($translationData['second_title'] ?? ''),
                        'second_title_subtitle' => trim($translationData['second_title_subtitle'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.suggest-a-place.edit')
                ->with('success', 'Suggest A Place section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Suggest A Place section: ' . $e->getMessage())
                ->withInput();
        }
    }
}
