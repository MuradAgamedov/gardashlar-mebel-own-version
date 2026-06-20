<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SuggestAPlaceFormRepositoryInterface;
use App\Models\SuggestAPlaceForm;
use App\Repositories\BaseRepository;

class SuggestAPlaceFormRepository extends BaseRepository implements SuggestAPlaceFormRepositoryInterface
{
    public function __construct(SuggestAPlaceForm $model)
    {
        parent::__construct($model);
    }
}
