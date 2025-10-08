<?php
namespace App\Repositories;

use App\Models\BlogCategory;
use App\Repositories\Global\BaseRepository;

class BlogCategoryRepository extends BaseRepository
{

    public function __construct(public BlogCategory $blogCategory)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getCategories()
    {
        return $this->GetData($this->blogCategory, ['translates.language'])->get();
    }
}
