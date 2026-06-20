<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Language\UpdateLanguageRequest;

use App\Http\Requests\Admin\Language\StoreLanguageRequest;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request)
    {
        $request->validated();

        $data = $request->all();
        $data['status'] = $request->has('status') ? true : false;
        $data['is_main_language'] = $request->has('is_main_language') ? true : false;

        $this->languageService->storeLanguage($data);

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        return view('admin.languages.show', compact('language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language)
    {
        $request->validated();

        $data = $request->all();
        $data['status'] = $request->has('status') ? true : false;
        $data['is_main_language'] = $request->has('is_main_language') ? true : false;

        try {
            $this->languageService->updateLanguage($language, $data);

            return redirect()->route('admin.languages.index')
                ->with('success', __('Language updated successfully.'));

        } catch (\InvalidArgumentException $e) {
            return redirect()->route('admin.languages.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        try {
            $languageName = $language->title;
            $this->languageService->deleteLanguage($language);

            return redirect()->route('admin.languages.index')
                ->with('success', __('Language ":lang" deleted successfully.', ['lang' => $languageName]));

        } catch (\InvalidArgumentException $e) {
            return redirect()->route('admin.languages.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.languages.index')
                ->with('error', __('An error occurred while deleting the language. Please try again.'));
        }
    }

    /**
     * Reorder languages
     */
    public function reorder(Request $request)
    {
        try {
            $languages = $request->input('languages', []);
            $this->languageService->reorderLanguages($languages);

            return response()->json([
                'success' => true,
                'message' => __('Languages reordered successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error reordering languages: :error', ['error' => $e->getMessage()])
            ], 500);
        }
    }
}