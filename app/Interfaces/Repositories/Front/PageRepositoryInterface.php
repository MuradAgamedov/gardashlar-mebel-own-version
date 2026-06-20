<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface PageRepositoryInterface extends BaseRepositoryInterface
{
    public function getPageByKeyword(string $keyword);
}
