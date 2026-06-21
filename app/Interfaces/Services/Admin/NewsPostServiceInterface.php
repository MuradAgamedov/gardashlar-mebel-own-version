<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface NewsPostServiceInterface extends BaseServiceInterface
{
    //

    public function getPosts($request);

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null);

    public function updatePost(\App\Models\NewsPost $newsPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null);

    public function deletePost(\App\Models\NewsPost $newsPost);

    public function bulkDelete(array $ids);
}
