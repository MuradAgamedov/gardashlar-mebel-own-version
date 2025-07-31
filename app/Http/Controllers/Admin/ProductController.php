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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'types', 'translates.language', 'tags']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->input('category'));
        }
        
        // Type filter
        if ($request->filled('type')) {
            $query->whereHas('types', function($q) use ($request) {
                $q->where('product_types.id', $request->input('type'));
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        $products = $query->ordered()->paginate(30);
        
        // Get categories and types for filters
        $categories = ProductCategory::active()->whereNotNull('parent_id')->ordered()->get();
        $types = ProductType::active()->ordered()->get();
        
        return view('admin.products.index', compact('products', 'categories', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::active()->whereNotNull('parent_id')->ordered()->get();
        $types = ProductType::active()->ordered()->get();
        $tags = ProductTag::active()->ordered()->get();
        $languages = Language::all();
        
        return view('admin.products.create', compact('categories', 'types', 'tags', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'product_types' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'translations.*.material' => 'nullable|string',
            'translations.*.size' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'product_tags' => 'nullable|string',
            'status' => 'boolean'
        ]);

        try {
            $product = Product::create([
                'product_category_id' => $request->input('product_category_id'),
                'discount_percent' => $request->input('discount_percent', 0),
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
                    'weight' => $translation['weight'] ?? null,
                    'material' => $translation['material'] ?? null,
                    'size' => $translation['size'] ?? null
                ]);
            }

            // Attach types
            if ($request->has('product_types') && !empty($request->input('product_types'))) {
                $types = json_decode($request->input('product_types'), true);
                if (is_array($types)) {
                    $product->types()->attach($types);
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
        $categories = ProductCategory::active()->whereNotNull('parent_id')->ordered()->get();
        $types = ProductType::active()->ordered()->get();
        $tags = ProductTag::active()->ordered()->get();
        $languages = Language::all();
        
        return view('admin.products.edit', compact('product', 'categories', 'types', 'tags', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'product_types' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
            'translations.*.material' => 'nullable|string',
            'translations.*.size' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'product_tags' => 'nullable|string',
            'status' => 'boolean'
        ]);

        try {
            $product->update([
                'product_category_id' => $request->input('product_category_id'),
                'discount_percent' => $request->input('discount_percent', 0),
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
                        'weight' => $translation['weight'] ?? null,
                        'material' => $translation['material'] ?? null,
                        'size' => $translation['size'] ?? null
                    ]
                );
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

            return redirect()->route('admin.products.index')
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
}
