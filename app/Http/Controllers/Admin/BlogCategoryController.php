<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogCategory\BulkDeleteBlogCategoryRequest;

use App\Http\Requests\Admin\BlogCategory\ReorderBlogCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Interfaces\Services\Admin\BlogCategoryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\BlogCategory\StoreBlogCategoryRequest;
use App\Http\Requests\Admin\BlogCategory\UpdateBlogCategoryRequest;

class BlogCategoryController extends Controller
{
    public function __construct(
        protected BlogCategoryServiceInterface $blogCategoryService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index(): View
    {
        $categories = $this->blogCategoryService->getCategories();
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.blog-categories.create', compact('languages'));
    }

    public function store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        try {
            $this->blogCategoryService->createCategory(
                ['status' => $request->input('status', true)],
                $request->input('translations')
            );

            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating blog category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(BlogCategory $blogCategory): View
    {
        $blogCategory->load('translates.language');
        $languages = $this->languageService->getActiveLanguages();
        $translations = $blogCategory->translates->keyBy('language_id');
        return view('admin.blog-categories.edit', compact('blogCategory', 'languages', 'translations'));
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory): RedirectResponse
    {
        try {
            $this->blogCategoryService->updateCategory(
                $blogCategory,
                ['status' => $request->input('status', true)],
                $request->input('translations')
            );

            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating blog category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        try {
            $this->blogCategoryService->deleteCategory($blogCategory);
            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog category: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderBlogCategoryRequest $request)
    {
        $request->validated();

        try {
            $this->blogCategoryService->reorderCategories($request->categories);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(BulkDeleteBlogCategoryRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = $this->blogCategoryService->bulkDelete($request->ids);
            return redirect()->route('admin.blog-categories.index')
                ->with('success', "{$deletedCount} blog category(ies) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog categories: ' . $e->getMessage());
        }
    }
}
