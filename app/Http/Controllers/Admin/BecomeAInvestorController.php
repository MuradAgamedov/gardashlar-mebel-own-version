<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestor;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;

class BecomeAInvestorController extends Controller
{
    public function __construct(public LanguageRepository $languageRepository)
    {
    }

    /**
     * Show the form for editing the investor section.
     */
    public function edit()
    {
        $becomeAInvestor = BecomeAInvestor::getFirstOrCreate();
        $becomeAInvestor->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.become-a-investor.edit', compact('becomeAInvestor', 'languages'));
    }

    /**
     * Update the investor section.
     */
    public function update(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.content" => 'required|string|max:5000',
            ], [
                "translations.{$language->id}.content.required" => "Content is required for {$language->lang_code}.",
                "translations.{$language->id}.content.max" => "Content may not be greater than 5000 characters for {$language->lang_code}.",
            ]);
        }

        try {
            $becomeAInvestor = BecomeAInvestor::getFirstOrCreate();

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $becomeAInvestor->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', 'Become A Investor section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Become A Investor section: ' . $e->getMessage())
                ->withInput();
        }
    }
}
