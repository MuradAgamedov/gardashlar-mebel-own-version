<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductTag;
use App\Models\ProductTagTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productTags = ProductTag::with('translates.language')
            ->ordered()
            ->get();
            
        return view('admin.product-tags.index', compact('productTags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.product-tags.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'status' => 'boolean'
        ]);

        try {
            $productTag = ProductTag::create([
                'status' => $request->boolean('status', true)
            ]);

            // Create translations
            foreach ($request->input('translations') as $translation) {
                ProductTagTranslate::create([
                    'product_tag_id' => $productTag->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'],
                    'slug' => $translation['slug']
                ]);
            }

            return redirect()->route('admin.product-tags.index')
                ->with('success', 'Product tag added successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductTag $productTag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductTag $productTag)
    {
        $languages = Language::all();
        $productTag->load('translates.language');
        
        return view('admin.product-tags.edit', compact('productTag', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductTag $productTag)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'status' => 'boolean'
        ]);

        try {
            $productTag->update([
                'status' => $request->boolean('status', true)
            ]);

            // Update translations
            foreach ($request->input('translations') as $translation) {
                ProductTagTranslate::updateOrCreate(
                    [
                        'product_tag_id' => $productTag->id,
                        'language_id' => $translation['language_id']
                    ],
                    [
                        'title' => $translation['title'],
                        'slug' => $translation['slug']
                    ]
                );
            }

            return redirect()->route('admin.product-tags.index')
                ->with('success', 'Product tag updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductTag $productTag)
    {
        try {
            $productTag->delete();

            return redirect()->route('admin.product-tags.index')
                ->with('success', 'Product tag deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Generate slug from title
     */
    public function generateSlug(Request $request)
    {
        $title = $request->input('title');
        $slug = Str::slug($title);
        
        return response()->json(['slug' => $slug]);
    }

    /**
     * Reorder tags
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:product_tags,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                ProductTag::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
