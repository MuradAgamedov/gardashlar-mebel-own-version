<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ReportServiceInterface extends BaseServiceInterface
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
