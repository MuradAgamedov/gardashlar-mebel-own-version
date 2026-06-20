<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Dictionary\UpdateDictionaryRequest;

use App\Http\Requests\Admin\Dictionary\StoreDictionaryRequest;

use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use App\Models\DictionaryTranslate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Interfaces\Services\Admin\DictionaryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class DictionaryController extends Controller
{
    public function __construct(
        protected DictionaryServiceInterface $dictionaryService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $dictionaries = $this->dictionaryService->getDictionaries($request);
        $languages = $this->languageService->getActiveLanguages();
        
        return view('admin.dictionaries.index', compact('dictionaries', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.dictionaries.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDictionaryRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $dictionary = Dictionary::create([
                'keyword' => $request->keyword,
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['value'])) {
                    DictionaryTranslate::create([
                        'dictionary_id' => $dictionary->id,
                        'language_id' => $languageId,
                        'value' => $translation['value'],
                    ]);
                }
            }

            return redirect()->route('admin.dictionaries.index', ['page' => $request->input('page', 1)])
                ->with('success', __('Dictionary item created successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating dictionary item. Please try again.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dictionary $dictionary): View
    {
        $languages = $this->languageService->getActiveLanguages();
        $translations = $dictionary->translates->keyBy('language_id');
        return view('admin.dictionaries.edit', compact('dictionary', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDictionaryRequest $request, Dictionary $dictionary): RedirectResponse
    {
        $request->validated();

        try {
            $dictionary->update([
                'keyword' => $request->keyword,
            ]);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['value'])) {
                    DictionaryTranslate::updateOrCreate(
                        [
                            'dictionary_id' => $dictionary->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'value' => $translation['value'],
                        ]
                    );
                }
            }

            return redirect()->route('admin.dictionaries.index', ['page' => $request->input('page', 1)])
                ->with('success', __('Dictionary item updated successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating dictionary item. Please try again.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dictionary $dictionary): RedirectResponse
    {
        try {
            $dictionary->delete();
            return redirect()->route('admin.dictionaries.index', ['page' => $request->input('page', 1)])
                ->with('success', __('Dictionary item deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting dictionary item. Please try again.'));
        }
    }
}
