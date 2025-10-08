<?php
namespace App\Repositories;

use App\Models\BlogImage;
use App\Models\BlogPost;
use App\Repositories\Global\BaseRepository;

class BlogImageRepository extends BaseRepository
{

    public function __construct(public BlogImage $blogImage)
    {
    }

    // Blog post üçün şəkilləri gətir
    public function getImagesByPost(BlogPost $blogPost)
    {
        return $blogPost->images()->get();
    }
}
