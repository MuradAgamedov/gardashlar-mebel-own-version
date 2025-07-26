<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::with(['translates.language', 'parent.translates.language', 'children.translates.language']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('seo_title', 'like', "%{$search}%");
            });
        }
        
        $categories = $query->ordered()->get();
            
        // Get all categories for parent selection in forms
        $allCategories = ProductCategory::with('translates.language')->ordered()->get();
        
        return view('admin.product-categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::active()->orderBy('order')->get();
        $categories = ProductCategory::with('translates.language')->root()->ordered()->get();
        return view('admin.product-categories.create', compact('languages', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'parent_id' => 'nullable|exists:product_categories,id',
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
        ]);

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('product-categories', $imageName, 'public');
            }

            // Create the category
            $category = ProductCategory::create([
                'parent_id' => $request->input('parent_id'),
                'status' => $request->has('status') ? true : false,
                'image' => $imagePath,
            ]);

            // Create translations for each language
            foreach ($request->translates as $translate) {
                $category->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'slug' => trim($translate['slug']),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                    'image_alt_text' => trim($translate['image_alt_text'] ?? ''),
                ]);
            }

            return redirect()->route('admin.product-categories.index')
                ->with('success', 'Product category created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating product category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load('translates.language');
        return view('admin.product-categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        $productCategory->load('translates.language');
        $languages = Language::active()->orderBy('order')->get();
        $categories = ProductCategory::with('translates.language')
            ->where('id', '!=', $productCategory->id) // Prevent self-selection
            ->root()
            ->ordered()
            ->get();
        return view('admin.product-categories.edit', compact('productCategory', 'languages', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        // Validate the request
        $request->validate([
            'parent_id' => 'nullable|exists:product_categories,id',
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
        ]);

        try {
            // Handle image upload
            $imagePath = $productCategory->image;
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($productCategory->image && Storage::disk('public')->exists($productCategory->image)) {
                    Storage::disk('public')->delete($productCategory->image);
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('product-categories', $imageName, 'public');
            }

            // Update the category
            $productCategory->update([
                'parent_id' => $request->input('parent_id'),
                'status' => $request->has('status') ? true : false,
                'image' => $imagePath,
            ]);

            // Update translations for each language
            foreach ($request->translates as $translate) {
                $translation = $productCategory->translates()
                    ->where('language_id', $translate['language_id'])
                    ->first();

                if ($translation) {
                    $translation->update([
                        'title' => trim($translate['title']),
                        'slug' => trim($translate['slug']),
                        'seo_title' => trim($translate['seo_title'] ?? ''),
                        'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                        'seo_description' => trim($translate['seo_description'] ?? ''),
                        'image_alt_text' => trim($translate['image_alt_text'] ?? ''),
                    ]);
                } else {
                    // Create new translation if doesn't exist
                    $productCategory->translates()->create([
                        'language_id' => $translate['language_id'],
                        'title' => trim($translate['title']),
                        'slug' => trim($translate['slug']),
                        'seo_title' => trim($translate['seo_title'] ?? ''),
                        'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                        'seo_description' => trim($translate['seo_description'] ?? ''),
                        'image_alt_text' => trim($translate['image_alt_text'] ?? ''),
                    ]);
                }
            }

            return redirect()->route('admin.product-categories.index')
                ->with('success', 'Product category updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating product category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        try {
            $productCategory->delete();
            return redirect()->route('admin.product-categories.index')
                ->with('success', 'Product category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting product category: ' . $e->getMessage());
        }
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request)
    {
        try {
            $items = $request->input('items', []);
            $type = $request->input('type', 'main'); // 'main' or 'sub'
            
            if ($type === 'main') {
                // Reorder main categories (parent_id = null)
                foreach ($items as $index => $itemId) {
                    ProductCategory::where('id', $itemId)
                        ->whereNull('parent_id')
                        ->update(['order' => $index + 1]);
                }
            } else {
                // Reorder subcategories (parent_id != null)
                foreach ($items as $index => $itemId) {
                    ProductCategory::where('id', $itemId)
                        ->whereNotNull('parent_id')
                        ->update(['order' => $index + 1]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete selected categories
     */
    public function deleteSelected(Request $request)
    {
        try {
            $selectedIds = $request->input('selected_ids', []);
            
            // If selected_ids is a JSON string, decode it
            if (is_string($selectedIds)) {
                $selectedIds = json_decode($selectedIds, true) ?? [];
            }
            
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'No categories selected for deletion.');
            }

            // Check if any selected category has children
            $categoriesWithChildren = ProductCategory::whereIn('id', $selectedIds)
                ->whereHas('children')
                ->get();

            if ($categoriesWithChildren->isNotEmpty()) {
                $categoryNames = $categoriesWithChildren->map(function($category) {
                    $firstTranslation = $category->translates->first();
                    return $firstTranslation ? $firstTranslation->title : 'Untitled';
                })->implode(', ');
                
                return redirect()->back()->with('error', "Cannot delete categories with subcategories: {$categoryNames}");
            }

            // Delete the categories
            ProductCategory::whereIn('id', $selectedIds)->delete();

            return redirect()->route('admin.product-categories.index')
                ->with('success', count($selectedIds) . ' categories deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting categories: ' . $e->getMessage());
        }
    }


} 