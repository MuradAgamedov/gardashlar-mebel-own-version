<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostTranslate;
use App\Models\BlogCategory;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogPostController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogPosts = BlogPost::with(['category.translates', 'translates.language'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.blog-posts.index', compact('blogPosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BlogCategory::with('translates')->active()->ordered()->get();
        $languages = Language::active()->orderBy('order')->get();

        return view('admin.blog-posts.create', compact('categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'status' => 'boolean',
            'published_at' => 'nullable|date',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'inner_banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255|unique:blog_post_translates,slug',
            'translations.*.description' => 'required|string',
            'translations.*.card_image_alt_text' => 'nullable|string|max:255',
            'translations.*.card_description' => 'nullable|string',
            'translations.*.inner_banner_alt_text' => 'nullable|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
        ]);

        try {
            $data = [
                'blog_category_id' => $request->blog_category_id,
                'status' => $request->input('status', false),
                'published_at' => $request->published_at,
            ];

            // Handle card image upload
            if ($request->hasFile('card_image')) {
                $data['card_image'] = $this->imageService->upload($request->file('card_image'), 'blog-posts');
            }

            // Handle inner banner image upload
            if ($request->hasFile('inner_banner_image')) {
                $data['inner_banner_image'] = $this->imageService->upload($request->file('inner_banner_image'), 'blog-posts');
            }

            $blogPost = BlogPost::create($data);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    // Generate slug from title
                    $slug = Str::slug($translation['title']);
                    $originalSlug = $slug;
                    $counter = 1;
                    
                    // Check if slug already exists
                    while (BlogPostTranslate::where('slug', $slug)->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    BlogPostTranslate::create([
                        'blog_post_id' => $blogPost->id,
                        'language_id' => $languageId,
                        'title' => $translation['title'],
                        'slug' => $slug,
                        'description' => $translation['description'],
                        'card_image_alt_text' => $translation['card_image_alt_text'] ?? null,
                        'card_description' => $translation['card_description'] ?? null,
                        'inner_banner_alt_text' => $translation['inner_banner_alt_text'] ?? null,
                        'seo_title' => $translation['seo_title'] ?? null,
                        'seo_keywords' => $translation['seo_keywords'] ?? null,
                        'seo_description' => $translation['seo_description'] ?? null,
                    ]);
                }
            }

            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating blog post: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating blog post. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blogPost)
    {
        $blogPost->load(['category.translates', 'translates.language']);
        $categories = BlogCategory::with('translates')->active()->ordered()->get();
        $languages = Language::active()->orderBy('order')->get();

        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'status' => 'boolean',
            'published_at' => 'nullable|date',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'inner_banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255|unique:blog_post_translates,slug,' . $blogPost->id . ',blog_post_id',
            'translations.*.description' => 'required|string',
            'translations.*.card_image_alt_text' => 'nullable|string|max:255',
            'translations.*.card_description' => 'nullable|string',
            'translations.*.inner_banner_alt_text' => 'nullable|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_keywords' => 'nullable|string',
            'translations.*.seo_description' => 'nullable|string',
        ]);

        try {
            $data = [
                'blog_category_id' => $request->blog_category_id,
                'status' => $request->input('status', false),
                'published_at' => $request->published_at,
            ];

            // Handle card image upload
            if ($request->hasFile('card_image')) {
                if ($blogPost->card_image) {
                    $this->imageService->delete($blogPost->card_image);
                }
                $data['card_image'] = $this->imageService->upload($request->file('card_image'), 'blog-posts');
            }

            // Handle inner banner image upload
            if ($request->hasFile('inner_banner_image')) {
                if ($blogPost->inner_banner_image) {
                    $this->imageService->delete($blogPost->inner_banner_image);
                }
                $data['inner_banner_image'] = $this->imageService->upload($request->file('inner_banner_image'), 'blog-posts');
            }

            $blogPost->update($data);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    // Generate slug from title
                    $slug = Str::slug($translation['title']);
                    $originalSlug = $slug;
                    $counter = 1;
                    
                    // Check if slug already exists (excluding current translation)
                    while (BlogPostTranslate::where('slug', $slug)
                        ->where('blog_post_id', '!=', $blogPost->id)
                        ->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    BlogPostTranslate::updateOrCreate(
                        [
                            'blog_post_id' => $blogPost->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'title' => $translation['title'],
                            'slug' => $slug,
                            'description' => $translation['description'],
                            'card_image_alt_text' => $translation['card_image_alt_text'] ?? null,
                            'card_description' => $translation['card_description'] ?? null,
                            'inner_banner_alt_text' => $translation['inner_banner_alt_text'] ?? null,
                            'seo_title' => $translation['seo_title'] ?? null,
                            'seo_keywords' => $translation['seo_keywords'] ?? null,
                            'seo_description' => $translation['seo_description'] ?? null,
                        ]
                    );
                }
            }

            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating blog post: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating blog post. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        try {
            // Delete images
            if ($blogPost->card_image) {
                $this->imageService->delete($blogPost->card_image);
            }
            if ($blogPost->inner_banner_image) {
                $this->imageService->delete($blogPost->inner_banner_image);
            }

            $blogPost->delete();
            return redirect()->route('admin.blog-posts.index')
                ->with('success', 'Blog post deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting blog post: ' . $e->getMessage());
            return back()->with('error', 'Error deleting blog post. Please try again.');
        }
    }

    /**
     * Bulk delete blog posts
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:blog_posts,id',
        ]);

        try {
            $blogPosts = BlogPost::whereIn('id', $request->ids)->get();
            
            foreach ($blogPosts as $blogPost) {
                if ($blogPost->card_image) {
                    $this->imageService->delete($blogPost->card_image);
                }
                if ($blogPost->inner_banner_image) {
                    $this->imageService->delete($blogPost->inner_banner_image);
                }
            }

            $deletedCount = BlogPost::whereIn('id', $request->ids)->delete();
            
            return redirect()->route('admin.blog-posts.index')
                ->with('success', "{$deletedCount} blog post(s) deleted successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting blog posts: ' . $e->getMessage());
        }
    }
}
