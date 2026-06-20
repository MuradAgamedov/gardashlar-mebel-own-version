<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\NewsCategory\BulkDeleteNewsCategoryRequest;

use App\Http\Requests\Admin\NewsCategory\ReorderNewsCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Interfaces\Services\Admin\NewsCategoryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\NewsCategory\StoreNewsCategoryRequest;
use App\Http\Requests\Admin\NewsCategory\UpdateNewsCategoryRequest;

class NewsCategoryController extends Controller
{
    public function __construct(
        protected NewsCategoryServiceInterface $newsCategoryService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index(): View
    {
        $categories = $this->newsCategoryService->getCategories();
        return view('admin.news-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.news-categories.create', compact('languages'));
    }

    public function store(StoreNewsCategoryRequest $request): RedirectResponse
    {
        try {
            $this->newsCategoryService->createCategory(
                ['status' => $request->input('status', true)],
                $request->input('translations')
            );

            return redirect()->route('admin.news-categories.index')
                ->with('success', 'News category created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating news category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(NewsCategory $newsCategory): View
    {
        $newsCategory->load('translates.language');
        $languages = $this->languageService->getActiveLanguages();
        $translations = $newsCategory->translates->keyBy('language_id');
        return view('admin.news-categories.edit', compact('newsCategory', 'languages', 'translations'));
    }

    public function update(UpdateNewsCategoryRequest $request, NewsCategory $newsCategory): RedirectResponse
    {
        try {
            $this->newsCategoryService->updateCategory(
                $newsCategory,
                ['status' => $request->input('status', true)],
                $request->input('translations')
            );

            return redirect()->route('admin.news-categories.index')
                ->with('success', 'News category updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating news category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(NewsCategory $newsCategory): RedirectResponse
    {
        try {
            $this->newsCategoryService->deleteCategory($newsCategory);
            return redirect()->route('admin.news-categories.index')
                ->with('success', 'News category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news category: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderNewsCategoryRequest $request)
    {
        $request->validated();

        try {
            $this->newsCategoryService->reorderCategories($request->categories);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(BulkDeleteNewsCategoryRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = $this->newsCategoryService->bulkDelete($request->ids);
            return redirect()->route('admin.news-categories.index')
                ->with('success', "{$deletedCount} news category(ies) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news categories: ' . $e->getMessage());
        }
    }
}
