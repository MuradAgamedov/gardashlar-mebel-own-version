<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface BlogImageRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getImagesByPost(\App\Models\BlogPost $blogPost);

    public function storeImages(\App\Models\BlogPost $blogPost, array $images);

    public function updateImage(\App\Models\BlogImage $blogImage, $imageFile);

    public function deleteImage(\App\Models\BlogImage $blogImage);

    public function reorderImages(\App\Models\BlogPost $blogPost, array $images);
}
