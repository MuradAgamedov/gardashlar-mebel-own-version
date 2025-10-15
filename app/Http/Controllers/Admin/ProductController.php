<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTranslate;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\ProductTag;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Repositories\ProductRepository;
use App\Repositories\LanguageRepository;

class ProductController extends Controller
{
    public function __construct(
        public ProductRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = $this->repository->getProducts($request);
        $categories = $this->repository->getActiveSubcategories();
        $types = $this->repository->getActiveTypes();
        
        return view('admin.products.index', compact('products', 'categories', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->repository->getActiveSubcategories();
        $types = $this->repository->getActiveTypes();
        $tags = $this->repository->getActiveTags();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.products.create', compact('categories', 'types', 'tags', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_categories' => 'nullable|string',
            'product_types' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric',
            'product_tags' => 'nullable|string',
            'status' => 'boolean'
        ]);

        try {
            $product = Product::create([
                'price' => $request->input('price'),
                'discount_price' => $request->input('discount_price', 0),
                'status' => $request->boolean('status', true)
            ]);

            // Create translations
            foreach ($request->input('translations') as $translation) {
                ProductTranslate::create([
                    'product_id' => $product->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? null,
                    'slug' => $translation['slug'],
                    'seo_title' => $translation['seo_title'] ?? null,
                    'seo_keywords' => $translation['seo_keywords'] ?? null,
                    'seo_description' => $translation['seo_description'] ?? null,
                ]);
            }

            // Attach categories
            if ($request->has('product_categories') && !empty($request->input('product_categories'))) {
                $categories = json_decode($request->input('product_categories'), true);
                if (is_array($categories)) {
                    $product->categories()->attach($categories);
                }
            }

            // Attach types - if no types selected, automatically assign the first type
            if ($request->has('product_types') && !empty($request->input('product_types'))) {
                $types = json_decode($request->input('product_types'), true);
                if (is_array($types)) {
                    $product->types()->attach($types);
                }
            } else {
                // If no types selected, automatically assign the first available type
                $firstType = ProductType::active()->ordered()->first();
                if ($firstType) {
                    $product->types()->attach([$firstType->id]);
                }
            }

            // Attach tags
            if ($request->has('product_tags') && !empty($request->input('product_tags'))) {
                $tags = json_decode($request->input('product_tags'), true);
                if (is_array($tags)) {
                    $product->tags()->attach($tags);
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product added successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['translates.language', 'tags']);
        $categories = $this->repository->getActiveSubcategories();
        $types = $this->repository->getActiveTypes();
        $tags = $this->repository->getActiveTags();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.products.edit', compact('product', 'categories', 'types', 'tags', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_categories' => 'nullable|string',
            'product_types' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric',
            'product_tags' => 'nullable|string',
            'status' => 'boolean'
        ]);

        try {
            $product->update([
                'price' => $request->input('price'),
                'discount_price' => $request->input('discount_price', 0),
                'status' => $request->boolean('status', true)
            ]);

            // Update translations
            foreach ($request->input('translations') as $translation) {
                ProductTranslate::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'language_id' => $translation['language_id']
                    ],
                    [
                        'title' => $translation['title'],
                        'description' => $translation['description'] ?? null,
                        'slug' => $translation['slug'],
                        'seo_title' => $translation['seo_title'] ?? null,
                        'seo_keywords' => $translation['seo_keywords'] ?? null,
                        'seo_description' => $translation['seo_description'] ?? null,
                    ]
                );
            }

            // Sync categories
            if ($request->has('product_categories') && !empty($request->input('product_categories'))) {
                $categories = json_decode($request->input('product_categories'), true);
                if (is_array($categories)) {
                    $product->categories()->sync($categories);
                } else {
                    $product->categories()->detach();
                }
            } else {
                $product->categories()->detach();
            }

            // Sync types
            if ($request->has('product_types') && !empty($request->input('product_types'))) {
                $types = json_decode($request->input('product_types'), true);
                if (is_array($types)) {
                    $product->types()->sync($types);
                } else {
                    $product->types()->detach();
                }
            } else {
                $product->types()->detach();
            }

            // Sync tags
            if ($request->has('product_tags') && !empty($request->input('product_tags'))) {
                $tags = json_decode($request->input('product_tags'), true);
                if (is_array($tags)) {
                    $product->tags()->sync($tags);
                } else {
                    $product->tags()->detach();
                }
            } else {
                $product->tags()->detach();
            }

            return redirect()->route('admin.products.index', ['page' => $request->input('current_page', 1)])
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete selected products
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Product::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Seçilən məhsullar silindi!']);
        }
        return response()->json(['success' => false, 'message' => 'Heç bir məhsul seçilməyib!']);
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
     * Reorder products
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:products,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                Product::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function duplicate(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:product_categories,id'
        ]);
    
        try {
            // Yeni product yaradılır (əsas məlumatlar)
            $newProduct = $product->replicate(['view_count', 'order']);
            $newProduct->status = false; // Yeni məhsul default olaraq deaktiv olsun
            $newProduct->save();
    
            // Translations (slug eynilə saxlanılır)
            foreach ($product->translates as $translate) {
                $newTranslate = $translate->replicate();
                $newTranslate->product_id = $newProduct->id;
                // ❌ slug dəyişmir
                $newTranslate->save();
            }
    
            // Yeni kateqoriya əlavə olunur
            $newProduct->categories()->attach($request->category_id);
    
            // Tiplər
            if ($product->types->count()) {
                $newProduct->types()->attach($product->types->pluck('id')->toArray());
            }
    
            // Tag-lar
            if ($product->tags->count()) {
                $newProduct->tags()->attach($product->tags->pluck('id')->toArray());
            }
    
            // Accordion-lar
            foreach ($product->accordions as $accordion) {
                $newAcc = $accordion->replicate();
                $newAcc->product_id = $newProduct->id;
                $newAcc->save();
            }
    
            // Detallar
            foreach ($product->details as $detail) {
                $newDetail = $detail->replicate();
                $newDetail->product_id = $newProduct->id;
                $newDetail->save();
            }
    
            // Detail values
            foreach ($product->detailValues as $dv) {
                $newDV = $dv->replicate();
                $newDV->product_id = $newProduct->id;
                $newDV->save();
            }
    
            // Şəkillər
            foreach ($product->images as $image) {
                $newImg = $image->replicate();
                $newImg->product_id = $newProduct->id;
                $newImg->save();
            }
    
            return redirect()->route('admin.products.index')
                ->with('success', 'Product duplicated successfully.');
    
        } catch (\Exception $e) {
            return back()->with('error', 'Error duplicating product: ' . $e->getMessage());
        }
    }
    

}
