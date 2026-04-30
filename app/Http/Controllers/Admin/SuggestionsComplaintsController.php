<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuggestionsComplaints;
use App\Models\Language;
use Illuminate\Http\Request;

class SuggestionsComplaintsController extends Controller
{
    /**
     * Show the form for editing the suggestions and complaints section.
     */
    public function edit()
    {
        $suggestionsComplaints = SuggestionsComplaints::getFirstOrCreate();
        $suggestionsComplaints->load('translates.language');
        $languages = Language::all();
        
        return view('admin.suggestions-complaints.edit', compact('suggestionsComplaints', 'languages'));
    }

    /**
     * Update the suggestions and complaints section.
     */
    public function update(Request $request)
    {
        $suggestionsComplaints = SuggestionsComplaints::getFirstOrCreate();
        $languages = Language::all();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
            ]);
        }

        // Update translations
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            
            $suggestionsComplaints->translates()->updateOrCreate(
                ['language_id' => $language->id],
                [
                    'title' => trim($translationData['title'] ?? ''),
                    'description' => trim($translationData['description'] ?? ''),
                ]
            );
        }

        return redirect()->route('admin.suggestions-complaints.edit')
            ->with('success', __('Suggestions and Complaints section updated successfully!'));
    }
}
