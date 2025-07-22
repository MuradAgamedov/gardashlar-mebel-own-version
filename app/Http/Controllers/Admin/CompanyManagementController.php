<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyManagement;
use App\Models\Language;
use Illuminate\Http\Request;

class CompanyManagementController extends Controller
{
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

            // Debug: Log all request data
            \Log::info('Company Management Update Request:', $request->all());

            // Update or create translations - only for languages that have data
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                
                // Debug: Log translation data for each language
                \Log::info("Translation data for {$language->lang_code}:", $translationData);
                
                // Only update if title or content is provided
                if (!empty($translationData['title']) || !empty($translationData['content'])) {
                    $companyManagement->translates()->updateOrCreate(
                        ['language_id' => $language->id],
                        [
                            'title' => trim($translationData['title'] ?? ''),
                            'content' => trim($translationData['content'] ?? ''),
                        ]
                    );
                    
                    \Log::info("Updated translation for {$language->lang_code}");
                }
            }

            return redirect()->back()
                ->with('success', 'Company Management updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Company Management Update Error:', ['error' => $e->getMessage()]);
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

            $file = $request->file('upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Create directory if it doesn't exist
            $uploadPath = 'uploads/ckeditor5';
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0755, true);
            }
            
            $file->move(public_path($uploadPath), $fileName);
            
            $url = asset($uploadPath . '/' . $fileName);
            
            // CKEditor 5 expects this response format
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
