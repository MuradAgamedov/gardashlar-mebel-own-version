<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\NewsPostRepositoryInterface;
use App\Interfaces\Services\Admin\NewsPostServiceInterface;
use App\Services\BaseService;

class NewsPostService extends BaseService implements NewsPostServiceInterface
{
    protected NewsPostRepositoryInterface $NewsPostRepository;

    public function __construct(NewsPostRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->NewsPostRepository = $repository;
    }

    public function getPosts($request)
    {
        return $this->NewsPostRepository->getPosts($request);
    }

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return $this->NewsPostRepository->createPost($data, $translations, $cardImage, $innerBannerImage);
    }

    public function updatePost(\App\Models\NewsPost $newsPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return $this->NewsPostRepository->updatePost($newsPost, $data, $translations, $cardImage, $innerBannerImage);
    }

    public function deletePost(\App\Models\NewsPost $newsPost)
    {
        return $this->NewsPostRepository->deletePost($newsPost);
    }

    public function bulkDelete(array $ids)
    {
        return $this->NewsPostRepository->bulkDelete($ids);
    }
}
