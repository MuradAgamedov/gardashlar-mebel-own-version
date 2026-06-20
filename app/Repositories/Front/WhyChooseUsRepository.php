<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\WhyChooseUsRepositoryInterface;
use App\Models\WhyChooseUs;
use App\Repositories\BaseRepository;

class WhyChooseUsRepository extends BaseRepository implements WhyChooseUsRepositoryInterface
{
    public function __construct(WhyChooseUs $model)
    {
        parent::__construct($model);
    }
}
