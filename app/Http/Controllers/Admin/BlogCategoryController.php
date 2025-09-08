<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = BlogCategory::with('translates.language')->orderBy('order')->get();
        return view('admin.blog-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.blog-categories.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $languages = Language::active()->orderBy('order')->get();
        
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
                "translations.{$language->id}.slug" => 'nullable|string|max:255',
                "translations.{$language->id}.seo_title" => 'nullable|string|max:255',
                "translations.{$language->id}.seo_keywords" => 'nullable|string',
                "translations.{$language->id}.seo_description" => 'nullable|string',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->title}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->title}.",
                "translations.{$language->id}.slug.max" => "Slug may not be greater than 255 characters for {$language->title}.",
                "translations.{$language->id}.seo_title.max" => "SEO title may not be greater than 255 characters for {$language->title}.",
            ]);
        }

        try {
            // Create blog category
            $blogCategory = BlogCategory::create([
                'status' => $request->input('status', true),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                BlogCategoryTranslate::create([
                    'blog_category_id' => $blogCategory->id,
                    'language_id' => $language->id,
                    'title' => trim($translationData['title'] ?? ''),
                    'slug' => trim($translationData['slug'] ?? ''),
                    'seo_title' => trim($translationData['seo_title'] ?? ''),
                    'seo_keywords' => trim($translationData['seo_keywords'] ?? ''),
                    'seo_description' => trim($translationData['seo_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating blog category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogCategory $blogCategory): View
    {
        $blogCategory->load('translates.language');
        $languages = Language::active()->orderBy('order')->get();
        $translations = $blogCategory->translates->keyBy('language_id');
        
        return view('admin.blog-categories.edit', compact('blogCategory', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        $languages = Language::active()->orderBy('order')->get();
        
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
                "translations.{$language->id}.slug" => 'nullable|string|max:255',
                "translations.{$language->id}.seo_title" => 'nullable|string|max:255',
                "translations.{$language->id}.seo_keywords" => 'nullable|string',
                "translations.{$language->id}.seo_description" => 'nullable|string',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->title}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->title}.",
                "translations.{$language->id}.slug.max" => "Slug may not be greater than 255 characters for {$language->title}.",
                "translations.{$language->id}.seo_title.max" => "SEO title may not be greater than 255 characters for {$language->title}.",
            ]);
        }

        try {
            // Update blog category
            $blogCategory->update([
                'status' => $request->input('status', true),
            ]);

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                BlogCategoryTranslate::updateOrCreate(
                    [
                        'blog_category_id' => $blogCategory->id,
                        'language_id' => $language->id,
                    ],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                        'slug' => trim($translationData['slug'] ?? ''),
                        'seo_title' => trim($translationData['seo_title'] ?? ''),
                        'seo_keywords' => trim($translationData['seo_keywords'] ?? ''),
                        'seo_description' => trim($translationData['seo_description'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating blog category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        try {
            $blogCategory->delete();
            
            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog category: ' . $e->getMessage());
        }
    }

    /**
     * Reorder blog categories
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:blog_categories,id',
        ]);

        try {
            foreach ($request->categories as $index => $categoryId) {
                BlogCategory::where('id', $categoryId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete blog categories
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:blog_categories,id',
        ]);

        try {
            $deletedCount = BlogCategory::whereIn('id', $request->ids)->delete();
            
            return redirect()->route('admin.blog-categories.index')
                ->with('success', "{$deletedCount} blog category(ies) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog categories: ' . $e->getMessage());
        }
    }
}
