<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BlogImageServiceInterface extends BaseServiceInterface
{
    //

    public function getImagesByPost(\App\Models\BlogPost $blogPost);

    public function storeImages(\App\Models\BlogPost $blogPost, array $images);

    public function updateImage(\App\Models\BlogImage $blogImage, $imageFile);

    public function deleteImage(\App\Models\BlogImage $blogImage);

    public function reorderImages(\App\Models\BlogPost $blogPost, array $images);
}
