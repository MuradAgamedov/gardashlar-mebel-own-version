<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use App\Models\DictionaryTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\DictionaryRepository;
use App\Repositories\LanguageRepository;

class DictionaryController extends Controller
{
    public function __construct(
        public DictionaryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $dictionaries = $this->repository->getDictionaries($request);
        $languages = $this->languageRepository->getActiveLanguages();
        
        return view('admin.dictionaries.index', compact('dictionaries', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.dictionaries.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:dictionaries',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.value' => 'required|string',
        ]);

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

            return redirect()->route('admin.dictionaries.index')
                ->with('success', 'Dictionary item created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating dictionary item. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dictionary $dictionary): View
    {
        $languages = $this->languageRepository->getActiveLanguages();
        $translations = $dictionary->translates->keyBy('language_id');
        return view('admin.dictionaries.edit', compact('dictionary', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dictionary $dictionary): RedirectResponse
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:dictionaries,keyword,' . $dictionary->id,
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.value' => 'required|string',
        ]);

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

            return redirect()->route('admin.dictionaries.index')
                ->with('success', 'Dictionary item updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating dictionary item. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dictionary $dictionary): RedirectResponse
    {
        try {
            $dictionary->delete();
            return redirect()->route('admin.dictionaries.index')
                ->with('success', 'Dictionary item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting dictionary item. Please try again.');
        }
    }
}
