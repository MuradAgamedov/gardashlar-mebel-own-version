<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyPolicy;
use App\Models\Language;
use Illuminate\Http\Request;

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

        // Translation validation - only for languages that have data
        foreach ($languages as $index => $language) {
            $translationData = $request->input("translations.{$index}", []);
            
            // Only validate if title or content is provided
            if (!empty($translationData['title']) || !empty($translationData['content'])) {
                $request->validate([
                    "translations.{$index}.title" => 'required|string|max:255',
                    "translations.{$index}.content" => 'required|string',
                ], [
                    "translations.{$index}.title.required" => "Title is required for {$language->lang_code}.",
                    "translations.{$index}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                    "translations.{$index}.content.required" => "Content is required for {$language->lang_code}.",
                ]);
            }
        }

        try {
            $warrantyPolicy = WarrantyPolicy::first();
            
            if (!$warrantyPolicy) {
                $warrantyPolicy = WarrantyPolicy::create([
                    'status' => true,
                ]);
            }

            // Update or create translations - only for languages that have data
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                
                // Only update if title or content is provided
                if (!empty($translationData['title']) || !empty($translationData['content'])) {
                    $warrantyPolicy->translates()->updateOrCreate(
                        ['language_id' => $language->id],
                        [
                            'title' => trim($translationData['title'] ?? ''),
                            'content' => trim($translationData['content'] ?? ''),
                        ]
                    );
                }
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
