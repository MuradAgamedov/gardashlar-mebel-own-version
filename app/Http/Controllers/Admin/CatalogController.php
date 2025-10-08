<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Language;
use App\Services\FileService;
use Illuminate\Http\Request;
use App\Repositories\CatalogRepository;
use App\Repositories\LanguageRepository;

class CatalogController extends Controller
{
    protected $fileService;

    public function __construct(
        FileService $fileService,
        public CatalogRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catalogs = $this->repository->getCatalogs();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.catalogs.index', compact('catalogs', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.catalogs.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Combined validation
        $validationRules = [
            'catalog_pdf' => 'required|file|mimes:pdf',
        ];
        
        $validationMessages = [
            'status.boolean' => 'Status must be true or false.',
            'catalog_pdf.required' => "Catalog PDF is required.",
            'catalog_pdf.file' => "Catalog PDF must be a file.",
            'catalog_pdf.mimes' => "Catalog PDF must be a PDF file.",
        ];

        // Add translation validation rules
        foreach ($languages as $language) {
            $validationRules["translations.{$language->id}.catalog_year"] = 'required|string|max:255';
            $validationMessages["translations.{$language->id}.catalog_year.required"] = "Catalog year is required for {$language->lang_code}.";
            $validationMessages["translations.{$language->id}.catalog_year.max"] = "Catalog year may not be greater than 255 characters for {$language->lang_code}.";
        }

        $request->validate($validationRules, $validationMessages);

        try {
            // Handle PDF upload
            $pdfPath = null;
            if ($request->hasFile('catalog_pdf')) {
                $pdfPath = $this->fileService->upload(
                    $request->file('catalog_pdf'),
                    'catalogs',
                    [
                        'allowed_types' => ['pdf'],
                        'generate_unique_name' => false, // Keep original filename
                    ]
                );
                
                if (!$pdfPath) {
                    return redirect()->back()
                        ->with('error', "Failed to upload PDF.")
                        ->withInput();
                }
            }

            // Create catalog
            $catalogData = [
                'status' => $request->input('status', true),
            ];
            
            if ($pdfPath) {
                $catalogData['catalog_pdf'] = $pdfPath;
            }
            
            $catalog = Catalog::create($catalogData);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $catalog->translates()->create([
                    'language_id' => $language->id,
                    'catalog_year' => trim($translationData['catalog_year'] ?? ''),
                ]);
            }

            return redirect()->route('admin.catalogs.index')
                ->with('success', 'Catalog created successfully!');

        } catch (\Exception $e) {
            \Log::error('Catalog creation error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return redirect()->back()
                ->with('error', 'Error creating catalog: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Catalog $catalog)
    {
        $catalog->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.catalogs.edit', compact('catalog', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Catalog $catalog)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Combined validation
        $validationRules = [
            'catalog_pdf' => 'nullable|file|mimes:pdf',
        ];
        
        $validationMessages = [
            'status.boolean' => 'Status must be true or false.',
            'catalog_pdf.file' => "Catalog PDF must be a file.",
            'catalog_pdf.mimes' => "Catalog PDF must be a PDF file.",
        ];

        // Add translation validation rules
        foreach ($languages as $language) {
            $validationRules["translations.{$language->id}.catalog_year"] = 'required|string|max:255';
            $validationMessages["translations.{$language->id}.catalog_year.required"] = "Catalog year is required for {$language->lang_code}.";
            $validationMessages["translations.{$language->id}.catalog_year.max"] = "Catalog year may not be greater than 255 characters for {$language->lang_code}.";
        }

        $request->validate($validationRules, $validationMessages);

        try {
            // Handle PDF upload
            $pdfPath = null;
            if ($request->hasFile('catalog_pdf')) {
                $uploadedPath = $this->fileService->update(
                    $request->file('catalog_pdf'),
                    $catalog->catalog_pdf,
                    'catalogs',
                    [
                        'allowed_types' => ['pdf'],
                        'generate_unique_name' => false, // Keep original filename
                    ]
                );
                
                if ($uploadedPath) {
                    $pdfPath = $uploadedPath;
                } else {
                    return redirect()->back()
                        ->with('error', "Failed to upload PDF.")
                        ->withInput();
                }
            }

            // Update catalog
            $catalog->update([
                'status' => $request->input('status', true),
                'catalog_pdf' => $pdfPath ?: $catalog->catalog_pdf,
            ]);

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                // Get existing translation
                $catalog->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'catalog_year' => trim($translationData['catalog_year'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.catalogs.index')
                ->with('success', 'Catalog updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating catalog: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Catalog $catalog)
    {
        try {
            // Delete the PDF file if it exists
            if ($catalog->catalog_pdf) {
                $this->fileService->delete($catalog->catalog_pdf);
            }
            
            $catalog->delete();
            return redirect()->route('admin.catalogs.index')
                ->with('success', 'Catalog deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting catalog: ' . $e->getMessage());
        }
    }

    /**
     * Reorder catalogs
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'catalogs' => 'required|array',
            'catalogs.*' => 'required|integer|exists:catalogs,id',
        ]);

        try {
            foreach ($request->catalogs as $index => $catalogId) {
                Catalog::where('id', $catalogId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 