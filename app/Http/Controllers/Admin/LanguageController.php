<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;

class LanguageController extends Controller
{
    public function __construct(public LanguageRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = $this->repository->getAllLanguages();
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
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'lang_code' => 'required|string|max:10|unique:languages',
            'lang_name' => 'required|string|max:255',
            'status' => 'boolean',
            'is_main_language' => 'boolean'
        ]);

        // Prepare data with proper boolean values
        $data = $request->all();
        $data['status'] = $request->has('status') ? true : false;
        $data['is_main_language'] = $request->has('is_main_language') ? true : false;

        // If this language is being set as main, unset all others
        if ($data['is_main_language']) {
            Language::where('is_main_language', true)->update(['is_main_language' => false]);
        }

        Language::create($data);

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
    public function update(Request $request, Language $language)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'lang_code' => 'required|string|max:10|unique:languages,lang_code,' . $language->id,
            'lang_name' => 'required|string|max:255',
            'status' => 'boolean',
            'is_main_language' => 'boolean'
        ]);

        // Get current values
        $currentStatus = $language->status;
        $currentIsMain = $language->is_main_language;

        // Prepare data with proper boolean values
        $data = $request->all();
        $newStatus = $request->has('status') ? true : false;
        $newIsMain = $request->has('is_main_language') ? true : false;

        // Check if trying to deactivate the main language
        if ($currentIsMain && !$newIsMain) {
            return redirect()->route('admin.languages.index')
                ->with('error', __('Cannot deactivate main language. Please set another language as main first.'));
        }

        // Check if trying to deactivate the only active language
        if ($currentStatus && !$newStatus) {
            $activeLanguagesCount = Language::where('status', true)->count();
            if ($activeLanguagesCount <= 1) {
                return redirect()->route('admin.languages.index')
                    ->with('error', __('Cannot deactivate the only active language. Please activate another language first.'));
            }
        }

        // Update the data array
        $data['status'] = $newStatus;
        $data['is_main_language'] = $newIsMain;

        // If this language is being set as main, unset all others
        if ($newIsMain && !$currentIsMain) {
            Language::where('is_main_language', true)->update(['is_main_language' => false]);
        }

        $language->update($data);

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        try {
            // Check if this is the main language
            if ($language->is_main_language) {
                return redirect()->route('admin.languages.index')
                    ->with('error', __('Cannot delete the main language. Please set another language as main first.'));
            }

            // Check if this is the only active language
            $activeLanguagesCount = Language::where('status', true)->count();
            if ($activeLanguagesCount <= 1 && $language->status) {
                return redirect()->route('admin.languages.index')
                    ->with('error', __('Cannot delete the only active language. Please activate another language first.'));
            }

            $languageName = $language->title;
            $language->delete();

            return redirect()->route('admin.languages.index')
                ->with('success', __('Language ":lang" deleted successfully.', ['lang' => $languageName]));

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
            \Log::info('Reorder request data:', $languages);
            
            foreach ($languages as $languageData) {
                $language = Language::find($languageData['id']);
                if ($language) {
                    \Log::info("Updating language {$language->id} order to {$languageData['order']}");
                    $language->update(['order' => $languageData['order']]);
                    \Log::info("Language {$language->id} updated successfully");
                } else {
                    \Log::warning("Language with ID {$languageData['id']} not found");
                }
            }
            
            \Log::info('Reorder completed successfully');
            return response()->json([
                'success' => true,
                'message' => __('Languages reordered successfully')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reorder error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Error reordering languages: :error', ['error' => $e->getMessage()])
            ], 500);
        }
    }
} 