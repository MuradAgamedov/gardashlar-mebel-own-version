<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SimpleImageRepositoryInterface;
use App\Models\SimpleImage;
use App\Repositories\BaseRepository;

class SimpleImageRepository extends BaseRepository implements SimpleImageRepositoryInterface
{
    public function __construct(SimpleImage $model)
    {
        parent::__construct($model);
    }
}
