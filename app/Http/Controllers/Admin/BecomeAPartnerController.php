<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartner;
use App\Models\Language;
use Illuminate\Http\Request;

class BecomeAPartnerController extends Controller
{
    /**
     * Show the form for editing the partner section.
     */
    public function edit()
    {
        $becomeAPartner = BecomeAPartner::getFirstOrCreate();
        $becomeAPartner->load('translates.language');
        $languages = Language::all();
        
        return view('admin.become-a-partner.edit', compact('becomeAPartner', 'languages'));
    }

    /**
     * Update the partner section.
     */
    public function update(Request $request)
    {
        $languages = Language::all();
        
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
            $becomeAPartner = BecomeAPartner::getFirstOrCreate();

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $becomeAPartner->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Become A Partner section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Become A Partner section: ' . $e->getMessage())
                ->withInput();
        }
    }
} 