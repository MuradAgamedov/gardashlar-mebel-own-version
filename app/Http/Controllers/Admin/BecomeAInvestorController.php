<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestor;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\BecomeAInvestorServiceInterface;
use App\Http\Requests\Admin\BecomeAInvestor\UpdateBecomeAInvestorRequest;

class BecomeAInvestorController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected BecomeAInvestorServiceInterface $becomeAInvestorService
    )
    {
    }

    public function edit()
    {
        $becomeAInvestor = $this->becomeAInvestorService->getFirstOrCreate();
        $becomeAInvestor->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.become-a-investor.edit', compact('becomeAInvestor', 'languages'));
    }

    public function update(UpdateBecomeAInvestorRequest $request)
    {
        try {
            $becomeAInvestor = $this->becomeAInvestorService->getFirstOrCreate();
            $this->becomeAInvestorService->updateBecomeAInvestor($becomeAInvestor, $request->input('translations'));

            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', 'Become A Investor section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Become A Investor section: ' . $e->getMessage())
                ->withInput();
        }
    }
}
