<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyManagement;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Services\ImageService;

class CompanyManagementController extends Controller
{
    protected $imageService;
    public function __construct(ImageService $imageService)
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
        
        $languages = Language::all();
        
        return view('admin.company-management.edit', compact('companyManagement', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = Language::all();

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
