<?php
namespace App\Repositories;

use App\Models\Page;
use App\Repositories\Global\BaseRepository;

class PageRepository extends BaseRepository
{

    public function __construct(public Page $page)
    {
    }

    // Bütün səhifələri gətir
    public function getPages()
    {
        return $this->page->with(['translates.language'])->orderBy('order')->get();
    }
}
