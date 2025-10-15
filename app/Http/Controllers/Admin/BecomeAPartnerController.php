<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartner;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use App\Repositories\BecomeAPartnerRepository;
use App\Http\Requests\Admin\BecomeAPartner\UpdateBecomeAPartnerRequest;

class BecomeAPartnerController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public BecomeAPartnerRepository $repository
    )
    {
    }

    public function edit()
    {
        $becomeAPartner = $this->repository->getFirstOrCreate();
        $becomeAPartner->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.become-a-partner.edit', compact('becomeAPartner', 'languages'));
    }

    public function update(UpdateBecomeAPartnerRequest $request)
    {
        try {
            $becomeAPartner = $this->repository->getFirstOrCreate();
            $this->repository->updateBecomeAPartner($becomeAPartner, $request->input('translations'));

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Become A Partner section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Become A Partner section: ' . $e->getMessage())
                ->withInput();
        }
    }
} 