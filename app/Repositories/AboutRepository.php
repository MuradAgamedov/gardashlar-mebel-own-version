<?php
namespace App\Repositories;

use App\Models\About;
use App\Repositories\Global\BaseRepository;

class AboutRepository extends BaseRepository
{

    public function __construct(public About $about)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAbout()
    {
        return $this->GetData($this->about, ['translates.language'])->get();
    }
}
