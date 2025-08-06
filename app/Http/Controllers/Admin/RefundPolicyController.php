<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundPolicy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RefundPolicyController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $refundPolicy = RefundPolicy::with(['translates.language'])->first();
        
        if (!$refundPolicy) {
            $refundPolicy = RefundPolicy::create(['status' => true]);
        }
        
        $languages = Language::all();
        
        return view('admin.refund-policy.edit', compact('refundPolicy', 'languages'));
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
            $refundPolicy = RefundPolicy::first();
            
            if (!$refundPolicy) {
                $refundPolicy = RefundPolicy::create([
                    'status' => true,
                ]);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $refundPolicy->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            // Clear cache for all languages
            $languages = Language::getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("refund_policy_{$language->id}");
            }

            return redirect()->back()
                ->with('success', 'Refund Policy updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating refund policy: ' . $e->getMessage())
                ->withInput();
        }
    }
}
