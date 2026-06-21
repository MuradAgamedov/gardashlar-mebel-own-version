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

    /**
     * Get all reports with translations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllReports()
    {
        return $this->ReportRepository->getAllReports();
    }

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->ReportRepository->getActiveLanguages();
    }
}
