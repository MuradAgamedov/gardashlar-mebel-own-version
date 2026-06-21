<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface NewsPostRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getPosts($request);

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null);

    public function updatePost(\App\Models\NewsPost $newsPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null);

    public function deletePost(\App\Models\NewsPost $newsPost);

    public function bulkDelete(array $ids);
}
