<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogCategoryRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\BlogPost\StoreBlogPostRequest;
use App\Http\Requests\Admin\BlogPost\UpdateBlogPostRequest;

class BlogPostController extends Controller
{
    public function __construct(
        public BlogPostRepository $repository,
        public BlogCategoryRepository $categoryRepository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index(Request $request)
    {
        $blogPosts = $this->repository->getPosts($request);
        $categories = $this->categoryRepository->getCategories();
        return view('admin.blog-posts.index', compact('blogPosts', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryRepository->getCategories();
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.blog-posts.create', compact('categories', 'languages'));
    }

    public function store(StoreBlogPostRequest $request)
    {
        try {
            $this->repository->createPost(
                [
                    'blog_category_id' => $request->blog_category_id,
                    'status' => $request->has('status'),
                    'published_at' => $request->published_at,
                ],
                $request->translations,
                $request->file('card_image'),
                $request->file('inner_banner_image')
            );

            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating blog post. Please try again.');
        }
    }

    public function edit(BlogPost $blogPost)
    {
        $blogPost->load(['category.translates', 'translates.language']);
        $categories = $this->categoryRepository->getCategories();
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'languages'));
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
    {
        try {
            $this->repository->updatePost(
                $blogPost,
                [
                    'blog_category_id' => $request->blog_category_id,
                    'status' => $request->has('status'),
                    'published_at' => $request->published_at,
                ],
                $request->translations,
                $request->file('card_image'),
                $request->file('inner_banner_image')
            );

            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating blog post. Please try again.');
        }
    }

    public function destroy(BlogPost $blogPost)
    {
        try {
            $this->repository->deletePost($blogPost);
            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog post. Please try again.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:blog_posts,id',
        ]);

        try {
            $deletedCount = $this->repository->bulkDelete($request->ids);
            return redirect()->route('admin.blog-posts.index')
                ->with('success', "{$deletedCount} blog post(s) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog posts: ' . $e->getMessage());
        }
    }
}
