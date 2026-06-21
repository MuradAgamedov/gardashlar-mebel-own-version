<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface SimpleImageRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getImages();
}
