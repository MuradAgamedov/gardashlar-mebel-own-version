<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Suggestion\DeleteSelectedSuggestionRequest;

use App\Http\Requests\Admin\Suggestion\ReorderSuggestionRequest;

use App\Http\Requests\Admin\Suggestion\UpdateSuggestionRequest;

use App\Http\Requests\Admin\Suggestion\StoreSuggestionRequest;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\SuggestionTranslate;
use App\Services\ImageService;
use App\Services\FileService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuggestionController extends Controller
{
    protected $fileService;

    public function __construct(protected LanguageServiceInterface $languageService, protected ImageService $imageService, FileService $fileService)
    {
$this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suggestions = Suggestion::with(['translates' => function($query) {
            $query->where('language_id', $this->languageService->getMainLanguage()->id);
        }])->ordered()->get();

        return view('admin.suggestions.index', compact('suggestions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.suggestions.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuggestionRequest $request)
    {
        $request->validated();

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

            // Clear cache for all languages
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("suggestions_{$language->id}");
                Cache::forget("suggestion_{$suggestion->id}_{$language->id}");
            }

            return redirect()->route('admin.suggestions.index')
                ->with('success', __('Suggestion created successfully.'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
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
        $languages = $this->languageService->getActiveLanguages();
        
        return view('admin.suggestions.edit', compact('suggestion', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuggestionRequest $request, string $id)
    {
        $suggestion = Suggestion::findOrFail($id);

        $request->validated();

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

            // Clear cache for all languages
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("suggestions_{$language->id}");
                Cache::forget("suggestion_{$suggestion->id}_{$language->id}");
            }

            return redirect()->route('admin.suggestions.index')
                ->with('success', __('Suggestion updated successfully.'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
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

            // Clear cache for all languages
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("suggestions_{$language->id}");
                Cache::forget("suggestion_{$suggestion->id}_{$language->id}");
            }

            return redirect()->route('admin.suggestions.index')
                ->with('success', __('Suggestion deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
        }
    }

    /**
     * Reorder suggestions
     */
    public function reorder(ReorderSuggestionRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->input('orders') as $order => $id) {
                Suggestion::where('id', $id)->update(['order' => $order + 1]);
            }

            // Clear cache for all languages
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("suggestions_{$language->id}");
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete selected suggestions
     */
    public function deleteSelected(DeleteSelectedSuggestionRequest $request)
    {
        $request->validated();

        try {
            $suggestions = Suggestion::whereIn('id', $request->input('ids'))->get();
            
            foreach ($suggestions as $suggestion) {
                // Delete image
                if ($suggestion->image) {
                    $this->imageService->delete($suggestion->image);
                }

                // Delete PDF
                if ($suggestion->pdf) {
                    $this->fileService->delete($suggestion->pdf);
                }

                $suggestion->delete();
            }

            // Clear cache for all languages
            $languages = $this->languageService->getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("suggestions_{$language->id}");
                foreach ($request->input('ids') as $id) {
                    Cache::forget("suggestion_{$id}_{$language->id}");
                }
            }

            return redirect()->route('admin.suggestions.index')
                ->with('success', __(':count suggestion(s) deleted successfully.', ['count' => count($request->input('ids'))]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
        }
    }
}
