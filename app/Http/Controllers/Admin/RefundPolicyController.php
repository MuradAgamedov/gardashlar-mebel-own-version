<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RefundPolicy\UpdateRefundPolicyRequest;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\RefundPolicyServiceInterface;
use App\Models\RefundPolicy;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefundPolicyController extends Controller
{
    protected $repository;

    public function __construct(protected LanguageServiceInterface $languageService, protected RefundPolicyServiceInterface $refundPolicyService)
    {
        $this->repository = $repository;
    }

    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $refundPolicy = $this->refundPolicyService->getRefundPolicyWithTranslations();
        $languages = $this->refundPolicyService->getActiveLanguages();
        
        return view('admin.refund-policy.edit', compact('refundPolicy', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRefundPolicyRequest $request)
    {
        $languages = $this->refundPolicyService->getActiveLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
        }

        try {
            DB::transaction(function () use ($request) {
                $refundPolicy = RefundPolicy::firstOrCreate(
                    [],
                    ['status' => true]
                );

                // Update translations
                foreach ($request->input('translations', []) as $languageId => $translation) {
                    if (!empty($translation['content'])) {
                        $refundPolicy->translates()->updateOrCreate(
                            ['language_id' => $languageId],
                            ['content' => trim($translation['content'])]
                        );
                    }
                }

                // Clear cache for all languages
                $languages = $this->languageService->getActiveLanguages();
                foreach ($languages as $language) {
                    Cache::forget("refund_policy_{$language->id}");
                }
            });
            
            return redirect()->back()
                ->with('success', __('Refund Policy updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating refund policy: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
