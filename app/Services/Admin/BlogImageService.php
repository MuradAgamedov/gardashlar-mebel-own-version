<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BlogImageRepositoryInterface;
use App\Interfaces\Services\Admin\BlogImageServiceInterface;
use App\Services\BaseService;

class BlogImageService extends BaseService implements BlogImageServiceInterface
{
    protected BlogImageRepositoryInterface $blogImageRepository;

    public function __construct(BlogImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogImageRepository = $repository;
    }

    public function getImagesByPost(\App\Models\BlogPost $blogPost)
    {
        return $this->blogImageRepository->getImagesByPost($blogPost);
    }

    public function storeImages(\App\Models\BlogPost $blogPost, array $images)
    {
        return $this->blogImageRepository->storeImages($blogPost, $images);
    }

    public function updateImage(\App\Models\BlogImage $blogImage, $imageFile)
    {
        return $this->blogImageRepository->updateImage($blogImage, $imageFile);
    }

    public function deleteImage(\App\Models\BlogImage $blogImage)
    {
        return $this->blogImageRepository->deleteImage($blogImage);
    }

    public function reorderImages(\App\Models\BlogPost $blogPost, array $images)
    {
        return $this->blogImageRepository->reorderImages($blogPost, $images);
    }
}
