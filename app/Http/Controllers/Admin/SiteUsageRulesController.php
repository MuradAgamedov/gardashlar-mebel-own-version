<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SiteUsageRules\UpdateSiteUsageRulesRequest;

use App\Http\Controllers\Controller;
use App\Models\SiteUsageRules;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use Illuminate\Support\Facades\Cache;

use App\Interfaces\Services\Admin\SiteUsageRulesServiceInterface;

class SiteUsageRulesController extends Controller
{
    public function __construct(
        protected SiteUsageRulesServiceInterface $siteUsageRulesService,
        protected LanguageServiceInterface $languageService
    ) {
    }


    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $siteUsageRules = SiteUsageRules::with(['translates.language'])->first();
        
        if (!$siteUsageRules) {
            $siteUsageRules = SiteUsageRules::create(['status' => true]);
        }
        
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.site-usage-rules.edit', compact('siteUsageRules', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSiteUsageRulesRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
        }

        try {
            $siteUsageRules = SiteUsageRules::first();
            
            if (!$siteUsageRules) {
                $siteUsageRules = SiteUsageRules::create([
                    'status' => true,
                ]);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $siteUsageRules->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            // Clear cache for all languages
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("site_usage_rules_{$language->id}");
            }

            return redirect()->back()
                ->with('success', __('Site Usage Rules updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating site usage rules: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
