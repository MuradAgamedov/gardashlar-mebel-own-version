<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\BlogCategoryRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\BlogCategory\StoreBlogCategoryRequest;
use App\Http\Requests\Admin\BlogCategory\UpdateBlogCategoryRequest;

class BlogCategoryController extends Controller
{
    public function __construct(
        public BlogCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index(): View
    {
        $categories = $this->repository->getCategories();
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.blog-categories.create', compact('languages'));
    }

    public function store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        try {
            $this->repository->createCategory(
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
        $languages = $this->languageRepository->getActiveLanguages();
        $translations = $blogCategory->translates->keyBy('language_id');
        return view('admin.blog-categories.edit', compact('blogCategory', 'languages', 'translations'));
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory): RedirectResponse
    {
        try {
            $this->repository->updateCategory(
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
            $this->repository->deleteCategory($blogCategory);
            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog category: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:blog_categories,id',
        ]);

        try {
            $this->repository->reorderCategories($request->categories);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:blog_categories,id',
        ]);

        try {
            $deletedCount = $this->repository->bulkDelete($request->ids);
            return redirect()->route('admin.blog-categories.index')
                ->with('success', "{$deletedCount} blog category(ies) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog categories: ' . $e->getMessage());
        }
    }
}
