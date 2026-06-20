<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ReportRepositoryInterface;
use App\Interfaces\Services\Front\ReportServiceInterface;
use App\Services\BaseService;

class ReportService extends BaseService implements ReportServiceInterface
{
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->reportRepository = $repository;
    }
}
