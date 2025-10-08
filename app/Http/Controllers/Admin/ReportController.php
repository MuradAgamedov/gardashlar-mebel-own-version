<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Language;
use App\Services\FileService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::with('translates.language')->orderBy('order')->get();
        $languages = Language::all();
        
        return view('admin.reports.index', compact('reports', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.reports.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();
        
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



        // Handle PDF upload
        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $this->fileService->upload($request->file('pdf'), 'reports');
        }

        // Create report
        $report = Report::create([
            'pdf' => $pdfPath,
            'status' => $request->input('status'),
        ]);

        // Create translations
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            
            $report->translates()->create([
                'language_id' => $language->id,
                'title' => trim($translationData['title'] ?? ''),
                'description' => trim($translationData['description'] ?? ''),
            ]);
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $report->load('translates.language');
        $languages = Language::all();
        
        return view('admin.reports.edit', compact('report', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $languages = Language::all();
        
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



        // Handle PDF upload
        $pdfData = [];
        if ($request->hasFile('pdf')) {
            // Delete old PDF
            if ($report->pdf) {
                $this->fileService->delete($report->pdf);
            }
            
            $pdfPath = $this->fileService->upload($request->file('pdf'), 'reports');
            $pdfData['pdf'] = $pdfPath;
        }

        // Update report
        $report->update(array_merge($pdfData, [
            'status' => $request->input('status'),
        ]));

        // Update translations
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            
            $report->translates()->updateOrCreate(
                ['language_id' => $language->id],
                [
                    'title' => trim($translationData['title'] ?? ''),
                    'description' => trim($translationData['description'] ?? ''),
                ]
            );
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully!');
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

        foreach ($request->reports as $index => $reportId) {
            Report::where('id', $reportId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
