<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SuggestionsComplaints\UpdateSuggestionsComplaintsRequest;

use App\Http\Controllers\Controller;
use App\Models\SuggestionsComplaints;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

use App\Interfaces\Services\Admin\SuggestionsComplaintsServiceInterface;

class SuggestionsComplaintsController extends Controller
{
    public function __construct(
        protected SuggestionsComplaintsServiceInterface $suggestionsComplaintsService,
        protected LanguageServiceInterface $languageService
    ) {
    }


    /**
     * Show the form for editing the suggestions and complaints section.
     */
    public function edit()
    {
        $suggestionsComplaints = SuggestionsComplaints::getFirstOrCreate();
        $suggestionsComplaints->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.suggestions-complaints.edit', compact('suggestionsComplaints', 'languages'));
    }

    /**
     * Update the suggestions and complaints section.
     */
    public function update(UpdateSuggestionsComplaintsRequest $request)
    {
        $suggestionsComplaints = SuggestionsComplaints::getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
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
