<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KivCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\KivCategoryRepository;
use App\Repositories\LanguageRepository;

class KivCategoryController extends Controller
{
    public function __construct(
        public KivCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->repository->getCategories();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.kiv-categories.index', compact('categories', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.kiv-categories.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Base validation
        $request->validate([
            'status' => 'boolean',
        ], [
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.subtitle" => 'nullable|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.subtitle.max" => "Subtitle may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        // Create category (slug will be auto-generated in model)
        $category = KivCategory::create([
            'status' => $request->input('status'),
        ]);

        // Create translations
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            
            $category->translates()->create([
                'language_id' => $language->id,
                'title' => trim($translationData['title'] ?? ''),
                'subtitle' => trim($translationData['subtitle'] ?? ''),
            ]);
        }

        return redirect()->route('admin.kiv-categories.index')
            ->with('success', 'KIV Category created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KivCategory $kivCategory)
    {
        $kivCategory->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.kiv-categories.edit', compact('kivCategory', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KivCategory $kivCategory)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Base validation
        $request->validate([
            'status' => 'boolean',
        ], [
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.subtitle" => 'nullable|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.subtitle.max" => "Subtitle may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        // Update category (slug will be auto-updated in model)
        $kivCategory->update([
            'status' => $request->input('status'),
        ]);

        // Update translations
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            
            $kivCategory->translates()->updateOrCreate(
                ['language_id' => $language->id],
                [
                    'title' => trim($translationData['title'] ?? ''),
                    'subtitle' => trim($translationData['subtitle'] ?? ''),
                ]
            );
        }

        return redirect()->route('admin.kiv-categories.index')
            ->with('success', 'KIV Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KivCategory $kivCategory)
    {
        try {
            // Check if category has KIVs
            if ($kivCategory->kivs()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete category that has KIVs. Please delete all KIVs first.');
            }

            $kivCategory->delete();
            return redirect()->route('admin.kiv-categories.index')
                ->with('success', 'KIV Category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting KIV category: ' . $e->getMessage());
        }
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:kiv_categories,id',
        ]);

        try {
            foreach ($request->categories as $index => $categoryId) {
                KivCategory::where('id', $categoryId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 