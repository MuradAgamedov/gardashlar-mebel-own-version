<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\LanguageRepository;

class ProductCategoryController extends Controller
{
    public function __construct(
        public ProductCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->repository->getCategories($request);
        $allCategories = $this->repository->getAllCategories();
        
        return view('admin.product-categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        $categories = $this->repository->getRootCategories();
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
            'translates.*.slug' => 'required|string|max:255|unique:product_category_translates,slug',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
            'translates.*.seo_text' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.slug.required' => 'Slug is required for all languages.',
            'translates.*.slug.unique' => 'This slug is already taken. Please choose a different one.',
        ]);

        try {
            // Handle image upload using ImageService
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imageService = new ImageService();
                $imagePath = $imageService->upload($request->file('image'), 'product-categories');
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
                    'seo_text' => trim($translate['seo_text'] ?? ''),
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
        $languages = $this->languageRepository->getActiveLanguages();
        $categories = $this->repository->getRootCategoriesExcept($productCategory->id);
        return view('admin.product-categories.edit', compact('productCategory', 'languages', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        // Get existing translations to check if slug has changed
        $existingTranslations = $productCategory->translates->keyBy('language_id');
        
        // Build validation rules dynamically
        $validationRules = [
            'parent_id' => 'nullable|exists:product_categories,id',
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
            'translates.*.seo_text' => 'nullable|string',
        ];

        // Add unique validation for slug only if it has changed
        foreach ($request->translates as $index => $translate) {
            $languageId = $translate['language_id'];
            $newSlug = trim($translate['slug'] ?? '');
            
            // Check if translation exists and if slug has changed
            if (isset($existingTranslations[$languageId])) {
                $existingSlug = $existingTranslations[$languageId]->slug;
                if ($newSlug !== $existingSlug) {
                    // Slug has changed, add unique validation
                    $validationRules["translates.{$index}.slug"] = 'required|string|max:255|unique:product_category_translates,slug';
                } else {
                    // Slug hasn't changed, no unique validation needed
                    $validationRules["translates.{$index}.slug"] = 'required|string|max:255';
                }
            } else {
                // New translation, add unique validation
                $validationRules["translates.{$index}.slug"] = 'required|string|max:255|unique:product_category_translates,slug';
            }
        }

        // Validate the request
        $request->validate($validationRules, [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.slug.required' => 'Slug is required for all languages.',
            'translates.*.slug.unique' => 'This slug is already taken. Please choose a different one.',
        ]);

        try {
            // Handle image upload using ImageService
            $imagePath = $productCategory->image;
            $imageService = new ImageService();
            
            // Check if user wants to remove existing image
            if ($request->has('remove_image') && $request->input('remove_image') == 1) {
                if ($productCategory->image) {
                    $imageService->delete($productCategory->image);
                }
                $imagePath = null;
            }
            // Handle new image upload
            elseif ($request->hasFile('image')) {
                $imagePath = $imageService->update($request->file('image'), $productCategory->image, 'product-categories');
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
                        'seo_text' => trim($translate['seo_text'] ?? ''),
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
                        'seo_text' => trim($translate['seo_text'] ?? ''),
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
            // Delete associated image using ImageService
            if ($productCategory->image) {
                $imageService = new ImageService();
                $imageService->delete($productCategory->image);
            }
            
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

            // Delete associated images using ImageService
            $imageService = new ImageService();
            $categoriesToDelete = ProductCategory::whereIn('id', $selectedIds)->get();
            
            foreach ($categoriesToDelete as $category) {
                if ($category->image) {
                    $imageService->delete($category->image);
                }
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