<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\CustomerRightsServiceInterface;
use App\Http\Requests\Admin\CustomerRights\UpdateCustomerRightsRequest;

class CustomerRightsController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected CustomerRightsServiceInterface $customerRightsService
    )
    {
    }

    public function edit()
    {
        $customerRights = $this->customerRightsService->getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.customer-rights.edit', compact('customerRights', 'languages'));
    }

    public function update(UpdateCustomerRightsRequest $request)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            $this->customerRightsService->updateCustomerRights($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', __('Customer rights updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating customer rights: ') . $e->getMessage())
                ->withInput();
        }
    }
}
