<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CompanyManagement\UploadImageCompanyManagementRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\CompanyManagementServiceInterface;

class CompanyManagementController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected CompanyManagementServiceInterface $companyManagementService
    )
    {
    }

    public function edit()
    {
        $companyManagement = $this->companyManagementService->getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.company-management.edit', compact('companyManagement', 'languages'));
    }

    public function update(Request $request)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            $this->companyManagementService->updateCompanyManagement($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', __('Company Management updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating company management: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function uploadImage(UploadImageCompanyManagementRequest $request)
    {
        try {
            $request->validated();

            $url = $this->companyManagementService->uploadImage($request->file('upload'));

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => ['message' => $e->getMessage()]
            ], 400);
        }
    }
}
