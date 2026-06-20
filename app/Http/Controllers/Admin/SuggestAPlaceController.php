<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SuggestAPlace\UpdateSuggestAPlaceRequest;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlace;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

use App\Interfaces\Services\Admin\SuggestAPlaceServiceInterface;

class SuggestAPlaceController extends Controller
{
    public function __construct(
        protected SuggestAPlaceServiceInterface $suggestAPlaceService,
        protected LanguageServiceInterface $languageService
    ) {
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $suggestAPlace = SuggestAPlace::getFirstOrCreate();
        $suggestAPlace->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.suggest-a-place.edit', compact('suggestAPlace', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuggestAPlaceRequest $request)
    {
        $suggestAPlace = SuggestAPlace::getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
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
                    ]
                );
            }

            return redirect()->route('admin.suggest-a-place.edit')
                ->with('success', __('Suggest A Place section updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating Suggest A Place section: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
