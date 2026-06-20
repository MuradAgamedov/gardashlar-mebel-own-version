<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Report\ReorderReportRequest;

use App\Http\Requests\Admin\Report\StoreReportRequest;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\ReportServiceInterface;
use App\Models\Report;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $repository;
    protected $fileService;

    public function __construct(protected ReportServiceInterface $reportService, FileService $fileService)
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = $this->reportService->getAllReports();
        $languages = $this->reportService->getActiveLanguages();
        
        return view('admin.reports.index', compact('reports', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->reportService->getActiveLanguages();
        return view('admin.reports.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportStoreReportRequest $request)
    {
        $languages = $this->reportService->getActiveLanguages();

        // Base validation
        $request->validated();

        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
        }

        try {
            DB::transaction(function () use ($request) {
                $data = ['status' => $request->input('status')];

                // Handle PDF upload
                if ($request->hasFile('pdf')) {
                    $data['pdf'] = $this->fileService->upload($request->file('pdf'), 'reports');
                }

                // Create report
                $report = Report::create($data);

                // Create translations
                foreach ($request->input('translations', []) as $languageId => $translation) {
                    if (!empty($translation['title']) || !empty($translation['description'])) {
                        $report->translates()->create([
                            'language_id' => $languageId,
                            'title' => trim($translation['title']),
                            'description' => trim($translation['description']),
                        ]);
                    }
                }
            });

            return redirect()->route('admin.reports.index')
                ->with('success', 'Report created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating report: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $report->load('translates.language');
        $languages = $this->reportService->getActiveLanguages();

        return view('admin.reports.edit', compact('report', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $languages = $this->reportService->getActiveLanguages();

        // Base validation
        $request->validated();

        // Translation validation
        foreach ($languages as $language) {
            $request->validated();
        }

        try {
            DB::transaction(function () use ($request, $report) {
                $data = ['status' => $request->input('status')];

                // Handle PDF upload
                if ($request->hasFile('pdf')) {
                    if ($report->pdf) {
                        $this->fileService->delete($report->pdf);
                    }
                    $data['pdf'] = $this->fileService->upload($request->file('pdf'), 'reports');
                }

                // Update report
                $report->update($data);

                // Update translations
                foreach ($request->input('translations', []) as $languageId => $translation) {
                    $report->translates()->updateOrCreate(
                        ['language_id' => $languageId],
                        [
                            'title' => trim($translation['title']),
                            'description' => trim($translation['description']),
                        ]
                    );
                }
            });

            return redirect()->route('admin.reports.index')
                ->with('success', 'Report updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating report: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        try {
            DB::transaction(function () use ($report) {

                // Delete PDF file
                if ($report->pdf) {
                    $this->fileService->delete($report->pdf);
                }

                // Delete translations
                $report->translates()->delete();

                // Delete report
                $report->delete();
            });

            return redirect()->route('admin.reports.index')
                ->with('success', 'Report deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting report: ' . $e->getMessage());
        }
    }

    /**
     * Reorder reports
     */
    public function reorder(ReorderReportRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->reports as $index => $reportId) {
                Report::where('id', $reportId)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error reordering reports: ' . $e->getMessage()
            ], 500);
        }
    }
}
