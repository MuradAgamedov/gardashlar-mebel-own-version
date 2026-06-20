<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\HomePageDiscountServiceInterface;
use App\Http\Requests\Admin\HomePageDiscount\UpdateHomePageDiscountRequest;

class HomePageDiscountController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected HomePageDiscountServiceInterface $homePageDiscountService
    )
    {
    }

    public function edit()
    {
        $homePageDiscount = $this->homePageDiscountService->getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.home-page-discount.edit', compact('homePageDiscount', 'languages'));
    }

    public function update(UpdateHomePageDiscountRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();

        try {
            $this->homePageDiscountService->updateHomePageDiscount(
                $request->input('translations', []),
                $languages,
                $request->file('bg_image')
            );

            return redirect()->back()
                ->with('success', __('Home Page Discount section updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating home page discount section: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
