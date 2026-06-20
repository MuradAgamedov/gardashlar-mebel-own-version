<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;

interface PageServiceInterface extends BaseServiceInterface
{
    public function getPageByKeyword(string $keyword);
}
