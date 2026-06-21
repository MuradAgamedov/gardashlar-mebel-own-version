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
    public function __construct(protected LanguageServiceInterface $languageService, protected RefundPolicyServiceInterface $refundPolicyService)
    {
    }

    public function edit()
    {
        $refundPolicy = $this->refundPolicyService->getRefundPolicyWithTranslations();
        $languages = $this->languageService->getActiveLanguages();

        return view('admin.refund-policy.edit', compact('refundPolicy', 'languages'));
    }

    public function update(UpdateRefundPolicyRequest $request)
    {
        $request->validated();

        try {
            DB::transaction(function () use ($request) {
                $refundPolicy = RefundPolicy::firstOrCreate(
                    [],
                    ['status' => true]
                );

                foreach ($request->input('translations', []) as $languageId => $translation) {
                    $refundPolicy->translates()->updateOrCreate(
                        ['language_id' => $languageId],
                        ['content' => trim($translation['content'] ?? '')]
                    );
                }

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
