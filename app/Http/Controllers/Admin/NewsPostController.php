<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\NewsPost\BulkDeleteNewsPostRequest;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\NewsPostServiceInterface;
use App\Interfaces\Services\Admin\NewsCategoryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\NewsPost\StoreNewsPostRequest;
use App\Http\Requests\Admin\NewsPost\UpdateNewsPostRequest;

class NewsPostController extends Controller
{
    public function __construct(
        protected NewsPostServiceInterface $newsPostService,
        protected NewsCategoryServiceInterface $newsCategoryService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index(Request $request)
    {
        $newsPosts = $this->newsPostService->getPosts($request);
        $categories = $this->newsCategoryService->getCategories();
        return view('admin.news-posts.index', compact('newsPosts', 'categories'));
    }

    public function create()
    {
        $categories = $this->newsCategoryService->getCategories();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.news-posts.create', compact('categories', 'languages'));
    }

    public function store(StoreNewsPostRequest $request)
    {
        try {
            $this->newsPostService->createPost(
                [
                    'news_category_id' => $request->news_category_id,
                    'status' => $request->has('status'),
                    'published_at' => $request->published_at,
                ],
                $request->translations,
                $request->file('card_image'),
                $request->file('inner_banner_image')
            );

            return redirect()->route('admin.news-posts.index')
                ->with('success', 'News post created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating news post. Please try again.');
        }
    }

    public function edit(NewsPost $newsPost)
    {
        $newsPost->load(['category.translates', 'translates.language']);
        $categories = $this->newsCategoryService->getCategories();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.news-posts.edit', compact('newsPost', 'categories', 'languages'));
    }

    public function update(UpdateNewsPostRequest $request, NewsPost $newsPost)
    {
        try {
            $this->newsPostService->updatePost(
                $newsPost,
                [
                    'news_category_id' => $request->news_category_id,
                    'status' => $request->has('status'),
                    'published_at' => $request->published_at,
                ],
                $request->translations,
                $request->file('card_image'),
                $request->file('inner_banner_image')
            );

            return redirect()->route('admin.news-posts.index')
                ->with('success', 'News post updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating news post. Please try again.');
        }
    }

    public function destroy(NewsPost $newsPost)
    {
        try {
            $this->newsPostService->deletePost($newsPost);
            return redirect()->route('admin.news-posts.index')
                ->with('success', 'News post deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news post. Please try again.');
        }
    }

    public function bulkDelete(BulkDeleteNewsPostRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = $this->newsPostService->bulkDelete($request->ids);
            return redirect()->route('admin.news-posts.index')
                ->with('success', "{$deletedCount} news post(s) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news posts: ' . $e->getMessage());
        }
    }
}
