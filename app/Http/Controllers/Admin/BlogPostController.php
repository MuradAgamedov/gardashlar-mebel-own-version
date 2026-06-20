<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogPost\BulkDeleteBlogPostRequest;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\BlogPostServiceInterface;
use App\Interfaces\Services\Admin\BlogCategoryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\BlogPost\StoreBlogPostRequest;
use App\Http\Requests\Admin\BlogPost\UpdateBlogPostRequest;

class BlogPostController extends Controller
{
    public function __construct(
        protected BlogPostServiceInterface $blogPostService,
        protected BlogCategoryServiceInterface $blogCategoryService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index(Request $request)
    {
        $blogPosts = $this->blogPostService->getPosts($request);
        $categories = $this->blogCategoryService->getCategories();
        return view('admin.blog-posts.index', compact('blogPosts', 'categories'));
    }

    public function create()
    {
        $categories = $this->blogCategoryService->getCategories();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.blog-posts.create', compact('categories', 'languages'));
    }

    public function store(StoreBlogPostRequest $request)
    {
        try {
            $this->blogPostService->createPost(
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
        $categories = $this->blogCategoryService->getCategories();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'languages'));
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
    {
        try {
            $this->blogPostService->updatePost(
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
            $this->blogPostService->deletePost($blogPost);
            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog post. Please try again.');
        }
    }

    public function bulkDelete(BulkDeleteBlogPostRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = $this->blogPostService->bulkDelete($request->ids);
            return redirect()->route('admin.blog-posts.index')
                ->with('success', "{$deletedCount} blog post(s) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog posts: ' . $e->getMessage());
        }
    }
}
