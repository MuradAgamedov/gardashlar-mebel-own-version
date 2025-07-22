<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerRights;
use App\Models\Language;
use Illuminate\Http\Request;

class CustomerRightsController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $customerRights = CustomerRights::with(['translates.language'])->firstOrCreate();
        
        if (!$customerRights) {
            $customerRights = CustomerRights::create(['status' => true]);
        }
        
        $languages = Language::all();
        
        return view('admin.customer-rights.edit', compact('customerRights', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = Language::all();
        


        // Translation validation - only for languages that have data
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            
            // Only validate if title or content is provided
            if (!empty($translationData['title']) || !empty($translationData['content'])) {
                $request->validate([
                    "translations.{$language->id}.title" => 'required|string|max:255',
                    "translations.{$language->id}.content" => 'required|string',
                ], [
                    "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                    "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                    "translations.{$language->id}.content.required" => "Content is required for {$language->lang_code}.",
                ]);
            }
        }

        try {
            $customerRights = CustomerRights::first();
            
            if (!$customerRights) {
                $customerRights = CustomerRights::create([
                    'status' => true,
                ]);
            }

            // Update or create translations - only for languages that have data
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                // Only update if title or content is provided
                if (!empty($translationData['title']) || !empty($translationData['content'])) {
                    $customerRights->translates()->updateOrCreate(
                        ['language_id' => $language->id],
                        [
                            'title' => trim($translationData['title'] ?? ''),
                            'content' => trim($translationData['content'] ?? ''),
                        ]
                    );
                }
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
