<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\SuggestionTranslate;
use App\Models\Language;
use App\Services\ImageService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuggestionController extends Controller
{
    protected $imageService;
    protected $fileService;

    public function __construct(ImageService $imageService, FileService $fileService)
    {
        $this->imageService = $imageService;
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suggestions = Suggestion::with(['translates' => function($query) {
            $query->where('language_id', Language::getMainLanguage()->id);
        }])->ordered()->get();

        return view('admin.suggestions.index', compact('suggestions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.suggestions.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'boolean',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Upload image
            $imagePath = $this->imageService->upload($request->file('image'), 'suggestions');

            // Upload PDF if provided
            $pdfPath = null;
            if ($request->hasFile('pdf')) {
                $pdfPath = $this->fileService->upload($request->file('pdf'), 'suggestions/pdfs');
            }

            // Create suggestion
            $suggestion = Suggestion::create([
                'image' => $imagePath,
                'pdf' => $pdfPath,
                'status' => $request->input('status', true),
            ]);

            // Create translates
            foreach ($request->input('translates') as $translate) {
                SuggestionTranslate::create([
                    'suggestion_id' => $suggestion->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'image_alt_text' => $translate['image_alt_text'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.suggestions.index')
                ->with('success', 'Tövsiyyə uğurla əlavə edildi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $suggestion = Suggestion::with('translates.language')->findOrFail($id);
        return view('admin.suggestions.show', compact('suggestion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $suggestion = Suggestion::with('translates')->findOrFail($id);
        $languages = Language::getActiveLanguages();
        
        return view('admin.suggestions.edit', compact('suggestion', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $suggestion = Suggestion::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'boolean',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($suggestion->image) {
                    $this->imageService->delete($suggestion->image);
                }
                
                // Upload new image
                $imagePath = $this->imageService->upload($request->file('image'), 'suggestions');
                $suggestion->image = $imagePath;
            }

            // Handle PDF upload
            if ($request->hasFile('pdf')) {
                // Delete old PDF
                if ($suggestion->pdf) {
                    $this->fileService->delete($suggestion->pdf);
                }
                
                // Upload new PDF
                $pdfPath = $this->fileService->upload($request->file('pdf'), 'suggestions/pdfs');
                $suggestion->pdf = $pdfPath;
            }

            $suggestion->status = $request->input('status', true);
            $suggestion->save();

            // Update translates
            foreach ($request->input('translates') as $translate) {
                SuggestionTranslate::updateOrCreate(
                    [
                        'suggestion_id' => $suggestion->id,
                        'language_id' => $translate['language_id']
                    ],
                    [
                        'title' => $translate['title'],
                        'image_alt_text' => $translate['image_alt_text'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.suggestions.index')
                ->with('success', 'Tövsiyyə uğurla yeniləndi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $suggestion = Suggestion::findOrFail($id);

        try {
            // Delete image
            if ($suggestion->image) {
                $this->imageService->delete($suggestion->image);
            }

            // Delete PDF
            if ($suggestion->pdf) {
                $this->fileService->delete($suggestion->pdf);
            }

            $suggestion->delete();

            return redirect()->route('admin.suggestions.index')
                ->with('success', 'Tövsiyyə uğurla silindi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Reorder suggestions
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:suggestions,id'
        ]);

        try {
            foreach ($request->input('orders') as $order => $id) {
                Suggestion::where('id', $id)->update(['order' => $order + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
