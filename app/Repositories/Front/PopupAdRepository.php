<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PopupAdRepositoryInterface;
use App\Models\PopupAd;
use App\Repositories\BaseRepository;

class PopupAdRepository extends BaseRepository implements PopupAdRepositoryInterface
{
    public function __construct(PopupAd $model)
    {
        parent::__construct($model);
    }
}
