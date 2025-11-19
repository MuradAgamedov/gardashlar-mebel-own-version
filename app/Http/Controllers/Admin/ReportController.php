<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use App\Models\Report;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $repository;
    protected $fileService;

    public function __construct(ReportRepository $repository, FileService $fileService)
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = $this->repository->getAllReports();
        $languages = $this->repository->getActiveLanguages();
        
        return view('admin.reports.index', compact('reports', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->repository->getActiveLanguages();
        return view('admin.reports.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = $this->repository->getActiveLanguages();

        // Base validation
        $request->validate([
            'pdf' => 'nullable|file|mimes:pdf',
            'status' => 'boolean',
        ], [
            'pdf.mimes' => 'PDF must be a file of type: pdf.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
            ]);
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
        $languages = $this->repository->getActiveLanguages();

        return view('admin.reports.edit', compact('report', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $languages = $this->repository->getActiveLanguages();

        // Base validation
        $request->validate([
            'pdf' => 'nullable|file|mimes:pdf',
            'status' => 'boolean',
        ], [
            'pdf.mimes' => 'PDF must be a file of type: pdf.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
            ]);
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
    public function reorder(Request $request)
    {
        $request->validate([
            'reports' => 'required|array',
            'reports.*' => 'required|integer|exists:reports,id',
        ]);

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
