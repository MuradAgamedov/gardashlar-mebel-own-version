<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WarrantyPolicy\UpdateWarrantyPolicyRequest;

use App\Http\Controllers\Controller;
use App\Models\WarrantyPolicy;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use Illuminate\Support\Facades\Cache;

use App\Interfaces\Services\Admin\WarrantyPolicyServiceInterface;

class WarrantyPolicyController extends Controller
{
    public function __construct(
        protected WarrantyPolicyServiceInterface $warrantyPolicyService,
        protected LanguageServiceInterface $languageService
    ) {
    }


    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $warrantyPolicy = WarrantyPolicy::with(['translates.language'])->first();
        
        if (!$warrantyPolicy) {
            $warrantyPolicy = WarrantyPolicy::create(['status' => true]);
        }
        
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.warranty-policy.edit', compact('warrantyPolicy', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarrantyPolicyRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
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
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("warranty_policy_{$language->id}");
            }

            return redirect()->back()
                ->with('success', __('Warranty Policy updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating warranty policy: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
