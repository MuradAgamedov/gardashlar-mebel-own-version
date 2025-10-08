<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyManagement;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\ImageService;
use App\Repositories\LanguageRepository;

class CompanyManagementController extends Controller
{
    protected $imageService;
    
    public function __construct(ImageService $imageService, public LanguageRepository $languageRepository)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $companyManagement = CompanyManagement::with(['translates.language'])->first();
        
        if (!$companyManagement) {
            $companyManagement = CompanyManagement::create();
        }
        
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.company-management.edit', compact('companyManagement', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();

        try {
            $companyManagement = CompanyManagement::first();
            
            if (!$companyManagement) {
                $companyManagement = CompanyManagement::create();
            }

            // Update or create translations - only for languages that have data
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                
                // Only update if content is provided
                if (!empty($translationData['content'])) {
                    $companyManagement->translates()->updateOrCreate(
                        ['language_id' => $language->id],
                        [
                            'content' => trim($translationData['content'] ?? ''),
                        ]
                    );
                }
            }

            // Clear cache for all languages
            $languages = Language::getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("company_management_{$language->id}");
            }

            return redirect()->back()
                ->with('success', 'Company Management updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating company management: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Upload image for CKEditor 5
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $imagePath = $this->imageService->upload($request->file('upload'), 'ckeditor5');
            $url = asset($imagePath);

            return response()->json([
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }
}
