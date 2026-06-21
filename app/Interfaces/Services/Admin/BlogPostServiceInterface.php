<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BlogPostServiceInterface extends BaseServiceInterface
{
    //

    public function getPosts($request);

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null);

    public function updatePost(\App\Models\BlogPost $blogPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null);

    public function deletePost(\App\Models\BlogPost $blogPost);

    public function bulkDelete(array $ids);
}
