<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\VacancyFormRepositoryInterface;
use App\Models\VacancyForm;
use App\Repositories\BaseRepository;

class VacancyFormRepository extends BaseRepository implements VacancyFormRepositoryInterface
{
    public function __construct(VacancyForm $model)
    {
        parent::__construct($model);
    }
}
