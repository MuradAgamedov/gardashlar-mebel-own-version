<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use App\Repositories\NewsPostRepository;
use App\Repositories\NewsCategoryRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\NewsPost\StoreNewsPostRequest;
use App\Http\Requests\Admin\NewsPost\UpdateNewsPostRequest;

class NewsPostController extends Controller
{
    public function __construct(
        public NewsPostRepository $repository,
        public NewsCategoryRepository $categoryRepository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index(Request $request)
    {
        $newsPosts = $this->repository->getPosts($request);
        $categories = $this->categoryRepository->getCategories();
        return view('admin.news-posts.index', compact('newsPosts', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryRepository->getCategories();
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.news-posts.create', compact('categories', 'languages'));
    }

    public function store(StoreNewsPostRequest $request)
    {
        try {
            $this->repository->createPost(
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
        $categories = $this->categoryRepository->getCategories();
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.news-posts.edit', compact('newsPost', 'categories', 'languages'));
    }

    public function update(UpdateNewsPostRequest $request, NewsPost $newsPost)
    {
        try {
            $this->repository->updatePost(
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
            $this->repository->deletePost($newsPost);
            return redirect()->route('admin.news-posts.index')
                ->with('success', 'News post deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news post. Please try again.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:news_posts,id',
        ]);

        try {
            $deletedCount = $this->repository->bulkDelete($request->ids);
            return redirect()->route('admin.news-posts.index')
                ->with('success', "{$deletedCount} news post(s) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting news posts: ' . $e->getMessage());
        }
    }
}
