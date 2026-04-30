<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\LanguageRepository;
use App\Repositories\CustomerRightsRepository;
use App\Http\Requests\Admin\CustomerRights\UpdateCustomerRightsRequest;

class CustomerRightsController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public CustomerRightsRepository $repository
    )
    {
    }

    public function edit()
    {
        $customerRights = $this->repository->getFirstOrCreate();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.customer-rights.edit', compact('customerRights', 'languages'));
    }

    public function update(UpdateCustomerRightsRequest $request)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            $this->repository->updateCustomerRights($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', __('Customer rights updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating customer rights: ') . $e->getMessage())
                ->withInput();
        }
    }
}
