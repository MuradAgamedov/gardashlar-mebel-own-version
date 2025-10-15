<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestor;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use App\Repositories\BecomeAInvestorRepository;
use App\Http\Requests\Admin\BecomeAInvestor\UpdateBecomeAInvestorRequest;

class BecomeAInvestorController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public BecomeAInvestorRepository $repository
    )
    {
    }

    public function edit()
    {
        $becomeAInvestor = $this->repository->getFirstOrCreate();
        $becomeAInvestor->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.become-a-investor.edit', compact('becomeAInvestor', 'languages'));
    }

    public function update(UpdateBecomeAInvestorRequest $request)
    {
        try {
            $becomeAInvestor = $this->repository->getFirstOrCreate();
            $this->repository->updateBecomeAInvestor($becomeAInvestor, $request->input('translations'));

            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', 'Become A Investor section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Become A Investor section: ' . $e->getMessage())
                ->withInput();
        }
    }
}
