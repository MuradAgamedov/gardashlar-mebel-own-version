<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyPolicy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WarrantyPolicyController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $warrantyPolicy = WarrantyPolicy::with(['translates.language'])->first();
        
        if (!$warrantyPolicy) {
            $warrantyPolicy = WarrantyPolicy::create(['status' => true]);
        }
        
        $languages = Language::all();
        
        return view('admin.warranty-policy.edit', compact('warrantyPolicy', 'languages'));
    }

    /**
     * Update the specified resource in storage.
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
            $warrantyPolicy = WarrantyPolicy::first();
            
            if (!$warrantyPolicy) {
                $warrantyPolicy = WarrantyPolicy::create([
                    'status' => true,
                ]);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $warrantyPolicy->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            // Clear cache for all languages
            $languages = Language::getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("warranty_policy_{$language->id}");
            }

            return redirect()->back()
                ->with('success', 'Warranty Policy updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating warranty policy: ' . $e->getMessage())
                ->withInput();
        }
    }
}
