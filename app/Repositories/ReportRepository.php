<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Language;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    protected $report;
    protected $language;
    protected $fileService;

    public function __construct(Report $report, Language $language, FileService $fileService)
    {
        $this->report = $report;
        $this->language = $language;
        $this->fileService = $fileService;
    }

    /**
     * Get all reports with translations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllReports()
    {
        return $this->report->with('translates.language')
            ->orderBy('order')
            ->get();
    }

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->language->all();
    }
}
