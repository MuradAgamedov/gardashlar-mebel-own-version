<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $repository;

    public function __construct(ReportRepository $repository)
    {
        $this->repository = $repository;
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
            'pdf' => 'nullable|file|mimes:pdf', // 10MB max for PDF
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
            $this->repository->createReport(
                ['status' => $request->input('status')],
                $request->input('translations', []),
                $request->file('pdf')
            );

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
        $report->load('translations.language');
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
            $this->repository->updateReport(
                $report,
                ['status' => $request->input('status')],
                $request->input('translations', []),
                $request->file('pdf')
            );

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
            $this->repository->deleteReport($report);
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
            $this->repository->reorderReports($request->reports);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering reports: ' . $e->getMessage()
            ], 500);
        }
    }
}
