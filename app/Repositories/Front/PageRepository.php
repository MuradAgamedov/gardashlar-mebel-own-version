<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PageRepositoryInterface;
use App\Models\Page;
use App\Repositories\BaseRepository;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }

    public function getPageByKeyword(string $keyword)
    {
        return $this->model->newQuery()
            ->where('status', true)
            ->where('keyword', $keyword)
            ->with(['translates.language'])
            ->first();
    }
}
