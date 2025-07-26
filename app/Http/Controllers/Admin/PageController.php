<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Language;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::with(['translates.language'])->orderBy('order')->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.pages.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'status' => 'nullable',
            'keyword' => 'nullable|string|max:255',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.slug' => 'required|string|max:255|unique:page_translates,slug',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.slug.required' => 'Slug is required for all languages.',
            'translates.*.slug.unique' => 'This slug is already in use.',
        ]);

        try {
            // Create the page
            $page = Page::create([
                'status' => $request->has('status') ? true : false,
                'keyword' => trim($request->input('keyword', ''))
            ]);

            // Create translations for each language
            foreach ($request->translates as $translate) {
                $page->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'slug' => trim($translate['slug']),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.pages.index')
                ->with('success', 'Page created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating page: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = Page::with(['translates.language'])->findOrFail($id);
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = Page::with(['translates.language'])->findOrFail($id);
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.pages.edit', compact('page', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $page = Page::findOrFail($id);

        // Validate the request
        $request->validate([
            'status' => 'nullable',
            'keyword' => 'nullable|string|max:255',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.slug' => 'required|string|max:255|unique:page_translates,slug,' . $id . ',page_id',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.slug.required' => 'Slug is required for all languages.',
            'translates.*.slug.unique' => 'This slug is already in use.',
        ]);

        try {
            // Update the page
            $page->update([
                'status' => $request->has('status') ? true : false,
                'keyword' => trim($request->input('keyword', ''))
            ]);

            // Delete existing translations and create new ones
            $page->translates()->delete();

            // Create new translations
            foreach ($request->translates as $translate) {
                $page->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'slug' => trim($translate['slug']),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.pages.index')
                ->with('success', 'Page updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating page: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $page = Page::findOrFail($id);
            
            // Check if this is the only active page
            $activePagesCount = Page::where('status', true)->count();
            if ($activePagesCount <= 1 && $page->status) {
                return redirect()->back()
                    ->with('error', 'Cannot delete the only active page. Please activate another page first.');
            }
            
            $page->delete();

            return redirect()->route('admin.pages.index')
                ->with('success', 'Page deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting page: ' . $e->getMessage());
        }
    }

    /**
     * Reorder pages
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'pages' => 'required|array',
                'pages.*.id' => 'required|exists:pages,id',
                'pages.*.order' => 'required|integer|min:1',
            ]);

            $pages = $request->input('pages', []);
            
            foreach ($pages as $pageData) {
                $page = Page::find($pageData['id']);
                if ($page) {
                    $page->update(['order' => $pageData['order']]);
                }
            }

            return response()->json([
                'success' => true, 
                'message' => 'Pages reordered successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error reordering pages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate slug from title
     */
    public function generateSlug(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'language_id' => 'required|exists:languages,id',
                'page_id' => 'nullable|exists:pages,id',
            ]);

            $title = $request->input('title');
            $languageId = $request->input('language_id');
            $pageId = $request->input('page_id');
            
            // Return empty slug - let frontend handle slug generation
            $slug = '';

            return response()->json([
                'success' => true,
                'slug' => $slug
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating slug: ' . $e->getMessage()
            ], 500);
        }
    }
}
