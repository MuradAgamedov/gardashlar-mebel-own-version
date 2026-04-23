<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\NewsCategoryRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\NewsCategory\StoreNewsCategoryRequest;
use App\Http\Requests\Admin\NewsCategory\UpdateNewsCategoryRequest;

class NewsCategoryController extends Controller
{
    public function __construct(
        public NewsCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index(): View
    {
        $categories = $this->repository->getCategories();
        return view('admin.news-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.news-categories.create', compact('languages'));
    }

    public function store(StoreNewsCategoryRequest $request): RedirectResponse
    {
        try {
            $this->repository->createCategory(
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
        $languages = $this->languageRepository->getActiveLanguages();
        $translations = $newsCategory->translates->keyBy('language_id');
        return view('admin.news-categories.edit', compact('newsCategory', 'languages', 'translations'));
    }

    public function update(UpdateNewsCategoryRequest $request, NewsCategory $newsCategory): RedirectResponse
    {
        try {
            $this->repository->updateCategory(
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
            $this->repository->deleteCategory($newsCategory);
            return redirect()->route('admin.news-categories.index')
                ->with('success', 'News category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news category: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:news_categories,id',
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
            'ids.*' => 'required|integer|exists:news_categories,id',
        ]);

        try {
            $deletedCount = $this->repository->bulkDelete($request->ids);
            return redirect()->route('admin.news-categories.index')
                ->with('success', "{$deletedCount} news category(ies) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news categories: ' . $e->getMessage());
        }
    }
}
