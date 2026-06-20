<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ReportRepositoryInterface;
use App\Models\Report;
use App\Repositories\BaseRepository;

class ReportRepository extends BaseRepository implements ReportRepositoryInterface
{
    public function __construct(Report $model)
    {
        parent::__construct($model);
    }
}
