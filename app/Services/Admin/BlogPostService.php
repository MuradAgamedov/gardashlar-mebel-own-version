<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BlogPostRepositoryInterface;
use App\Interfaces\Services\Admin\BlogPostServiceInterface;
use App\Services\BaseService;

class BlogPostService extends BaseService implements BlogPostServiceInterface
{
    protected BlogPostRepositoryInterface $blogPostRepository;

    public function __construct(BlogPostRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogPostRepository = $repository;
    }

    public function getPosts($request)
    {
        return $this->blogPostRepository->getPosts($request);
    }

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return $this->blogPostRepository->createPost($data, $translations, $cardImage, $innerBannerImage);
    }

    public function updatePost(\App\Models\BlogPost $blogPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return $this->blogPostRepository->updatePost($blogPost, $data, $translations, $cardImage, $innerBannerImage);
    }

    public function deletePost(\App\Models\BlogPost $blogPost)
    {
        return $this->blogPostRepository->deletePost($blogPost);
    }

    public function bulkDelete(array $ids)
    {
        return $this->blogPostRepository->bulkDelete($ids);
    }
}
