<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\AboutRepositoryInterface;
use App\Models\About;
use App\Repositories\BaseRepository;

class AboutRepository extends BaseRepository implements AboutRepositoryInterface
{
    public function __construct(About $model)
    {
        parent::__construct($model);
    }
}
