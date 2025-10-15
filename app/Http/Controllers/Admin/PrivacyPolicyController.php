<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\LanguageRepository;
use App\Repositories\PrivacyPolicyRepository;
use App\Http\Requests\Admin\PrivacyPolicy\UpdatePrivacyPolicyRequest;

class PrivacyPolicyController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public PrivacyPolicyRepository $repository
    )
    {
    }

    public function edit()
    {
        $privacyPolicy = $this->repository->getOrCreatePrivacyPolicy();
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.privacy-policy.edit', compact('privacyPolicy', 'languages'));
    }

    public function update(UpdatePrivacyPolicyRequest $request)
    {
        try {
            $languages = $this->languageRepository->getActiveLanguages();
            $this->repository->updatePrivacyPolicy($request->input('translates'), $languages);

            return redirect()->route('admin.privacy-policy.edit')
                ->with('success', 'Gizlilik siyasəti uğurla yeniləndi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }
} 