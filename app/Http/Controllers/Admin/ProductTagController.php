<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductTag\ReorderProductTagRequest;

use App\Http\Requests\Admin\ProductTag\StoreProductTagRequest;

use App\Http\Controllers\Controller;
use App\Models\ProductTag;
use App\Models\ProductTagTranslate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Interfaces\Services\Admin\ProductTagServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class ProductTagController extends Controller
{
    public function __construct(
        protected ProductTagServiceInterface $productTagService,
        protected LanguageServiceInterface $languageService
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productTags = $this->productTagService->getTags();
            
        return view('admin.product-tags.index', compact('productTags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.product-tags.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductTagRequest $request)
    {
        $request->validated();

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
                ->with('success', __('Product tag added successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
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
        $languages = $this->languageService->getAllLanguages();
        $productTag->load('translates.language');
        
        return view('admin.product-tags.edit', compact('productTag', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductTag $productTag)
    {
        $request->validated();

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
                ->with('success', __('Product tag updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
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
                ->with('success', __('Product tag deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
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
    public function reorder(ReorderProductTagRequest $request)
    {
        $request->validated();

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
