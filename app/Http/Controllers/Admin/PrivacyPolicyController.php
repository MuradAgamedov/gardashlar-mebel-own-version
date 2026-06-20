<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\PrivacyPolicyServiceInterface;
use App\Http\Requests\Admin\PrivacyPolicy\UpdatePrivacyPolicyRequest;

class PrivacyPolicyController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected PrivacyPolicyServiceInterface $privacyPolicyService
    )
    {
    }

    public function edit()
    {
        $privacyPolicy = $this->privacyPolicyService->getOrCreatePrivacyPolicy();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.privacy-policy.edit', compact('privacyPolicy', 'languages'));
    }

    public function update(UpdatePrivacyPolicyRequest $request)
    {
        try {
            $languages = $this->languageService->getActiveLanguages();
            $this->privacyPolicyService->updatePrivacyPolicy($request->input('translates'), $languages);

            return redirect()->route('admin.privacy-policy.edit')
                ->with('success', __('Privacy Policy updated successfully!'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error updating privacy policy: :error', ['error' => $e->getMessage()]));
        }
    }
} 