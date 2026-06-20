<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SuggestAPlaceRepositoryInterface;
use App\Models\SuggestAPlace;
use App\Repositories\BaseRepository;

class SuggestAPlaceRepository extends BaseRepository implements SuggestAPlaceRepositoryInterface
{
    public function __construct(SuggestAPlace $model)
    {
        parent::__construct($model);
    }
}
