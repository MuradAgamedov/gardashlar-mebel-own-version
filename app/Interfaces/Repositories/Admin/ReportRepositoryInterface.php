<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ReportRepositoryInterface extends BaseRepositoryInterface
{
    //

    /**
     * Get all reports with translations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllReports();

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages();
}
