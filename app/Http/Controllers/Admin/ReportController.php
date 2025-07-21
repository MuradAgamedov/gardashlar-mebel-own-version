<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'pdf' => 'nullable|file|mimes:pdf|max:10240', // 10MB max for PDF
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'pdf.mimes' => 'PDF must be a file of type: pdf.',
            'pdf.max' => 'PDF may not be greater than 10MB.',
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

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->upload(
                $request->file('image'),
                'reports',
                [
                    'max_size' => 2048,
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                ]
            );
            
            if (!$imagePath) {
                return redirect()->back()
                    ->with('error', 'Failed to upload image.')
                    ->withInput();
            }
        }

        // Handle PDF upload
        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('reports', 'public');
        }

        // Create report
        $report = Report::create([
            'image' => $imagePath,
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'pdf' => 'nullable|file|mimes:pdf|max:10240', // 10MB max for PDF
            'status' => 'boolean',
        ], [
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif, webp.',
            'image.max' => 'Image may not be greater than 2MB.',
            'pdf.mimes' => 'PDF must be a file of type: pdf.',
            'pdf.max' => 'PDF may not be greater than 10MB.',
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

        // Handle image upload
        $imageData = [];
        if ($request->hasFile('image')) {
            $uploadedPath = $this->imageService->update(
                $request->file('image'),
                $report->image,
                'reports',
                [
                    'max_size' => 2048,
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                ]
            );
            
            if ($uploadedPath) {
                $imageData['image'] = $uploadedPath;
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to upload image.')
                    ->withInput();
            }
        }

        // Handle PDF upload
        $pdfData = [];
        if ($request->hasFile('pdf')) {
            // Delete old PDF
            if ($report->pdf) {
                Storage::disk('public')->delete($report->pdf);
            }
            
            $pdfPath = $request->file('pdf')->store('reports', 'public');
            $pdfData['pdf'] = $pdfPath;
        }

        // Update report
        $report->update(array_merge($imageData, $pdfData, [
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
