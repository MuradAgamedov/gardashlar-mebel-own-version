<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ReportRepositoryInterface;
use App\Interfaces\Services\Admin\ReportServiceInterface;
use App\Services\BaseService;

class ReportService extends BaseService implements ReportServiceInterface
{
    protected ReportRepositoryInterface $ReportRepository;

    public function __construct(ReportRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ReportRepository = $repository;
    }
}
