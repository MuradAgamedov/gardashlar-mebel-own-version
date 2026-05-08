<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Page;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate sitemap from database';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        /*
        |--------------------------------------------------------------------------
        | Static Pages
        |--------------------------------------------------------------------------
        */

        $staticPages = [
            '/',
            '/az',
            '/en',
            '/ru',
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create($page)
                    ->setPriority(1.0)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        if (class_exists(Category::class)) {

            Category::where('status', true)
                ->get()
                ->each(function ($category) use ($sitemap) {

                    $slug = is_array($category->slug)
                        ? ($category->slug['az'] ?? null)
                        : $category->slug;

                    if (!$slug) {
                        return;
                    }

                    $sitemap->add(
                        Url::create("/az/category/{$slug}")
                            ->setPriority(0.8)
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        if (class_exists(Product::class)) {

            Product::where('status', true)
                ->get()
                ->each(function ($product) use ($sitemap) {

                    $slug = is_array($product->slug)
                        ? ($product->slug['az'] ?? null)
                        : $product->slug;

                    if (!$slug) {
                        return;
                    }

                    $sitemap->add(
                        Url::create("/az/product/{$slug}")
                            ->setPriority(0.9)
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Blogs
        |--------------------------------------------------------------------------
        */

        if (class_exists(Blog::class)) {

            Blog::where('status', true)
                ->get()
                ->each(function ($blog) use ($sitemap) {

                    $slug = is_array($blog->slug)
                        ? ($blog->slug['az'] ?? null)
                        : $blog->slug;

                    if (!$slug) {
                        return;
                    }

                    $sitemap->add(
                        Url::create("/az/blog/{$slug}")
                            ->setPriority(0.7)
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */

        if (class_exists(Page::class)) {

            Page::where('status', true)
                ->get()
                ->each(function ($page) use ($sitemap) {

                    $slug = is_array($page->slug)
                        ? ($page->slug['az'] ?? null)
                        : $page->slug;

                    if (!$slug) {
                        return;
                    }

                    $sitemap->add(
                        Url::create("/az/{$slug}")
                            ->setPriority(0.6)
                    );
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $path = public_path('sitemap.xml');

        $sitemap->writeToFile($path);

        $this->info("Sitemap generated successfully!");
        $this->info($path);
    }
}