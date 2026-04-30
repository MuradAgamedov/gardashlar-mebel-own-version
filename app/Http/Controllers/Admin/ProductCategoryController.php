<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\Admin\ProductCategory\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    public function __construct(
        public ProductCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }
    public function index(Request $request)
    {
        $categories = $this->repository->getCategories($request);
        $allCategories = $this->repository->getAllCategories();
        return view('admin.product-categories.index', compact('categories', 'allCategories'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        $categories = $this->repository->getRootCategories();
        return view('admin.product-categories.create', compact('languages', 'categories'));
    }

    public function store(StoreProductCategoryRequest $request)
    {
        try {
            $this->repository->createProductCategory(
                [
                    'parent_id' => $request->input('parent_id'),
                    'status' => $request->has('status') ? true : false,
                ],
                $request->translates,
                $request->file('image')
            );

            return redirect()->route('admin.product-categories.index')
                ->with('success', __('Product category created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating product category: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function show(ProductCategory $productCategory)
    {
        $productCategory->load('translates.language');
        return view('admin.product-categories.show', compact('productCategory'));
    }

    public function edit(ProductCategory $productCategory)
    {
        $productCategory->load('translates.language');
        $languages = $this->languageRepository->getActiveLanguages();
        $categories = $this->repository->getRootCategoriesExcept($productCategory->id);
        return view('admin.product-categories.edit', compact('productCategory', 'languages', 'categories'));
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        try {
            $this->repository->updateProductCategory(
                $productCategory,
                [
                    'parent_id' => $request->input('parent_id'),
                    'status' => $request->has('status') ? true : false,
                ],
                $request->translates,
                $request->file('image'),
                $request->has('remove_image') && $request->input('remove_image') == 1
            );

            return redirect()->route('admin.product-categories.index')
                ->with('success', __('Product category updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating product category: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(ProductCategory $productCategory)
    {
        try {
            $this->repository->deleteProductCategory($productCategory);
            return redirect()->route('admin.product-categories.index')
                ->with('success', __('Product category deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting product category: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(Request $request)
    {
        try {
            $this->repository->reorderProductCategories(
                $request->input('items', []),
                $request->input('type', 'main')
            );
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteSelected(Request $request)
    {
        try {
            $selectedIds = $request->input('selected_ids', []);
            
            if (is_string($selectedIds)) {
                $selectedIds = json_decode($selectedIds, true) ?? [];
            }
            
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', __('No categories selected for deletion.'));
            }

            $count = $this->repository->deleteSelectedCategories($selectedIds);

            return redirect()->route('admin.product-categories.index')
                ->with('success', __(':count categories deleted successfully!', ['count' => $count]));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting categories: :error', ['error' => $e->getMessage()]));
        }
    }
}