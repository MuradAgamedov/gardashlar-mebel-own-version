<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use App\Repositories\CompanyManagementRepository;

class CompanyManagementController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public CompanyManagementRepository $repository
    )
    {
    }

    public function edit()
    {
        $companyManagement = $this->repository->getFirstOrCreate();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.company-management.edit', compact('companyManagement', 'languages'));
    }

    public function update(Request $request)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            $this->repository->updateCompanyManagement($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', 'Company Management updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating company management: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $url = $this->repository->uploadImage($request->file('upload'));

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => ['message' => $e->getMessage()]
            ], 400);
        }
    }
}
