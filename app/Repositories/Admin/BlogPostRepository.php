<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BlogPostRepositoryInterface;
use App\Models\BlogPost;
use App\Repositories\BaseRepository;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    public function __construct(BlogPost $model)
    {
        parent::__construct($model);
    }

    public function getPosts($request)
    {
        $query = $this->blogPost->with(['category.translates', 'translates.language']);

        if ($request->filled('category_id')) {
            $query->where('blog_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('translates', function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('card_description', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return DB::transaction(function () use ($data, $translations, $cardImage, $innerBannerImage) {
            if ($cardImage) {
                $data['card_image'] = $this->imageService->upload($cardImage, 'blog-posts');
            }

            if ($innerBannerImage) {
                $data['inner_banner_image'] = $this->imageService->upload($innerBannerImage, 'blog-posts');
            }

            $blogPost = $this->blogPost->create($data);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    BlogPostTranslate::create(array_merge(
                        ['blog_post_id' => $blogPost->id, 'language_id' => $languageId],
                        $translation
                    ));
                }
            }

            return $blogPost->load(['category.translates', 'translates.language']);
        });
    }

    public function updatePost(BlogPost $blogPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return DB::transaction(function () use ($blogPost, $data, $translations, $cardImage, $innerBannerImage) {
            if ($cardImage) {
                if ($blogPost->card_image) {
                    $this->imageService->delete($blogPost->card_image);
                }
                $data['card_image'] = $this->imageService->upload($cardImage, 'blog-posts');
            }

            if ($innerBannerImage) {
                if ($blogPost->inner_banner_image) {
                    $this->imageService->delete($blogPost->inner_banner_image);
                }
                $data['inner_banner_image'] = $this->imageService->upload($innerBannerImage, 'blog-posts');
            }

            $blogPost->update($data);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    BlogPostTranslate::updateOrCreate(
                        ['blog_post_id' => $blogPost->id, 'language_id' => $languageId],
                        $translation
                    );
                }
            }

            return $blogPost->load(['category.translates', 'translates.language']);
        });
    }

    public function deletePost(BlogPost $blogPost)
    {
        return DB::transaction(function () use ($blogPost) {
            if ($blogPost->card_image) {
                $this->imageService->delete($blogPost->card_image);
            }
            if ($blogPost->inner_banner_image) {
                $this->imageService->delete($blogPost->inner_banner_image);
            }

            return $blogPost->delete();
        });
    }

    public function bulkDelete(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $blogPosts = $this->blogPost->whereIn('id', $ids)->get();
            
            foreach ($blogPosts as $blogPost) {
                if ($blogPost->card_image) {
                    $this->imageService->delete($blogPost->card_image);
                }
                if ($blogPost->inner_banner_image) {
                    $this->imageService->delete($blogPost->inner_banner_image);
                }
            }

            return $this->blogPost->whereIn('id', $ids)->delete();
        });
    }
}
