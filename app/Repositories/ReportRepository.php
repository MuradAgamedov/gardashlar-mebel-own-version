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

    /**
     * Create a new report
     *
     * @param array $data
     * @param array $translations
     * @param mixed $pdfFile
     * @return \App\Models\Report
     * @throws \Exception
     */
    public function createReport(array $data, array $translations, $pdfFile = null)
    {
        return DB::transaction(function () use ($data, $translations, $pdfFile) {
            // Handle PDF upload
            if ($pdfFile) {
                $data['pdf'] = $this->fileService->upload($pdfFile, 'reports');
            }

            // Create report
            $report = $this->report->create($data);

            // Create translations
            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title']) || !empty($translation['description'])) {
                    $report->translations()->create([
                        'language_id' => $languageId,
                        'title' => trim($translation['title'] ?? ''),
                        'description' => trim($translation['description'] ?? ''),
                    ]);
                }
            }

            return $report->load('translations.language');
        });
    }

    /**
     * Update an existing report
     *
     * @param Report $report
     * @param array $data
     * @param array $translations
     * @param mixed $pdfFile
     * @return \App\Models\Report
     * @throws \Exception
     */
    public function updateReport(Report $report, array $data, array $translations, $pdfFile = null)
    {
        return DB::transaction(function () use ($report, $data, $translations, $pdfFile) {
            // Handle PDF upload
            if ($pdfFile) {
                // Delete old PDF
                if ($report->pdf) {
                    $this->fileService->delete($report->pdf);
                }
                $data['pdf'] = $this->fileService->upload($pdfFile, 'reports');
            }

            // Update report
            $report->update($data);

            // Update translations
            foreach ($translations as $languageId => $translation) {
                $report->translations()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'title' => trim($translation['title'] ?? ''),
                        'description' => trim($translation['description'] ?? ''),
                    ]
                );
            }

            return $report->load('translations.language');
        });
    }

    /**
     * Delete a report
     *
     * @param Report $report
     * @return bool
     * @throws \Exception
     */
    public function deleteReport(Report $report)
    {
        return DB::transaction(function () use ($report) {
            // Delete PDF file
            if ($report->pdf) {
                $this->fileService->delete($report->pdf);
            }

            // Delete translations
            $report->translations()->delete();

            // Delete report
            return $report->delete();
        });
    }

    /**
     * Reorder reports
     *
     * @param array $reportIds
     * @return bool
     */
    public function reorderReports(array $reportIds)
    {
        return DB::transaction(function () use ($reportIds) {
            foreach ($reportIds as $index => $reportId) {
                $this->report->where('id', $reportId)->update(['order' => $index + 1]);
            }
            return true;
        });
    }
}
