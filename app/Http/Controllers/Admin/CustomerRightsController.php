<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerRights;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Repositories\LanguageRepository;

class CustomerRightsController extends Controller
{
    public function __construct(public LanguageRepository $languageRepository)
    {
    }

    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $customerRights = CustomerRights::with(['translates.language'])->firstOrCreate();
        
        if (!$customerRights) {
            $customerRights = CustomerRights::create(['status' => true]);
        }
        
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.customer-rights.edit', compact('customerRights', 'languages'));
    }

    /**
     * Update the specified resource in storage.
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
            $customerRights = CustomerRights::first();
            
            if (!$customerRights) {
                $customerRights = CustomerRights::create([
                    'status' => true,
                ]);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $customerRights->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            // Clear cache for all languages
            $languages = Language::getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("customer_rights_{$language->id}");
            }

            return redirect()->back()
                ->with('success', 'Customer rights updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating customer rights: ' . $e->getMessage())
                ->withInput();
        }
    }
}
