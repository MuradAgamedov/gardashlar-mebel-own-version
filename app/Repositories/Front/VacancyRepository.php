<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\VacancyRepositoryInterface;
use App\Models\Vacancy;
use App\Repositories\BaseRepository;

class VacancyRepository extends BaseRepository implements VacancyRepositoryInterface
{
    public function __construct(Vacancy $model)
    {
        parent::__construct($model);
    }
}
