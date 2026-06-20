<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartner;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\BecomeAPartnerServiceInterface;
use App\Http\Requests\Admin\BecomeAPartner\UpdateBecomeAPartnerRequest;

class BecomeAPartnerController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected BecomeAPartnerServiceInterface $becomeAPartnerService
    )
    {
    }

    public function edit()
    {
        $becomeAPartner = $this->becomeAPartnerService->getFirstOrCreate();
        $becomeAPartner->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.become-a-partner.edit', compact('becomeAPartner', 'languages'));
    }

    public function update(UpdateBecomeAPartnerRequest $request)
    {
        try {
            $becomeAPartner = $this->becomeAPartnerService->getFirstOrCreate();
            $this->becomeAPartnerService->updateBecomeAPartner($becomeAPartner, $request->input('translations'));

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Become A Partner section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Become A Partner section: ' . $e->getMessage())
                ->withInput();
        }
    }
} 