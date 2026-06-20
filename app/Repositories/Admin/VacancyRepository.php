<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\VacancyRepositoryInterface;
use App\Models\Vacancy;
use App\Repositories\BaseRepository;

class VacancyRepository extends BaseRepository implements VacancyRepositoryInterface
{
    public function __construct(Vacancy $model)
    {
        parent::__construct($model);
    }
}
