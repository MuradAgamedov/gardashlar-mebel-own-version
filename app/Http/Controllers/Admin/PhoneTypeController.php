<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneType;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\PhoneTypeRepository;
use App\Repositories\LanguageRepository;

class PhoneTypeController extends Controller
{
    public function __construct(
        public PhoneTypeRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $phoneTypes = $this->repository->getPhoneTypes();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.phone-types.index', compact('phoneTypes', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.phone-types.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Combined validation
        $validationRules = [];
        $validationMessages = [];

        // Add translation validation rules
        foreach ($languages as $language) {
            $validationRules["translations.{$language->id}.title"] = 'required|string|max:255';
            $validationMessages["translations.{$language->id}.title.required"] = "Title is required for {$language->lang_code}.";
            $validationMessages["translations.{$language->id}.title.max"] = "Title may not be greater than 255 characters for {$language->lang_code}.";
        }

        $request->validate($validationRules, $validationMessages);

        try {
            // Create phone type
            $phoneType = PhoneType::create([
                'status' => $request->input('status', false),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $phoneType->translates()->create([
                    'language_id' => $language->id,
                    'title' => trim($translationData['title'] ?? ''),
                ]);
            }

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone type created successfully!');

        } catch (\Exception $e) {
            \Log::error('Phone type creation error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return redirect()->back()
                ->with('error', 'Error creating phone type: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhoneType $phoneType)
    {
        $phoneType->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.phone-types.edit', compact('phoneType', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PhoneType $phoneType)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Combined validation
        $validationRules = [];
        $validationMessages = [];

        // Add translation validation rules
        foreach ($languages as $language) {
            $validationRules["translations.{$language->id}.title"] = 'required|string|max:255';
            $validationMessages["translations.{$language->id}.title.required"] = "Title is required for {$language->lang_code}.";
            $validationMessages["translations.{$language->id}.title.max"] = "Title may not be greater than 255 characters for {$language->lang_code}.";
        }

        $request->validate($validationRules, $validationMessages);

        try {
            // Update phone type
            $phoneType->update([
                'status' => $request->input('status', false),
            ]);

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $phoneType->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    ['title' => trim($translationData['title'] ?? '')]
                );
            }

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone type updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Phone type update error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return redirect()->back()
                ->with('error', 'Error updating phone type: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhoneType $phoneType)
    {
        // Check if phone type ID is 3 - if so, prevent deletion
        if ($phoneType->id == 3) {
            return redirect()->back()
                ->with('error', 'This phone type cannot be deleted as it is protected.');
        }

        try {
            $phoneType->delete();
            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone type deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Phone type deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting phone type: ' . $e->getMessage());
        }
    }

    /**
     * Reorder phone types
     */
    public function reorder(Request $request)
    {
        try {
            $phoneTypes = $request->input('phone_types', []);
            
            foreach ($phoneTypes as $index => $phoneTypeId) {
                PhoneType::where('id', $phoneTypeId)->update(['order' => $index + 1]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Phone type reorder error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 