<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\LanguageRepository;
use App\Repositories\HomePageDiscountRepository;
use App\Http\Requests\Admin\HomePageDiscount\UpdateHomePageDiscountRequest;

class HomePageDiscountController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public HomePageDiscountRepository $repository
    ) {
    }

    public function edit()
    {
        $homePageDiscount = $this->repository->getFirstOrCreate();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.home-page-discount.edit', compact('homePageDiscount', 'languages'));
    }

    public function update(UpdateHomePageDiscountRequest $request)
    {
        $languages = $this->languageRepository->getAllLanguages();

        try {
            $this->repository->updateHomePageDiscount(
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
