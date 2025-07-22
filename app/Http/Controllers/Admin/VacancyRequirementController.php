<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyRequirement;
use App\Models\Vacancy;
use App\Models\Language;
use Illuminate\Http\Request;

class VacancyRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VacancyRequirement::with(['vacancy', 'translates.language']);
        
        // Filter by vacancy if specified
        if ($request->has('vacancy_id') && $request->vacancy_id) {
            $query->where('vacancy_id', $request->vacancy_id);
        }
        
        $requirements = $query->orderBy('order')->get();
        $vacancies = Vacancy::all(); // For filter dropdown
        
        return view('admin.vacancy-requirements.index', compact('requirements', 'vacancies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vacancies = Vacancy::all();
        $languages = Language::all();
        return view('admin.vacancy-requirements.create', compact('vacancies', 'languages'));
    }

    /**
     * Show the form for creating a new requirement for a specific vacancy.
     */
    public function createForVacancy($vacancy_id)
    {
        $vacancy = Vacancy::with(['requirements.translates.language', 'translates.language'])->findOrFail($vacancy_id);
        $languages = Language::all();
        return view('admin.vacancy-requirements.create', compact('vacancy', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'status' => 'boolean',
        ], [
            'vacancy_id.required' => 'Vacancy is required.',
            'vacancy_id.exists' => 'Selected vacancy does not exist.',
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

        try {
            // Create requirement
            $requirement = VacancyRequirement::create([
                'vacancy_id' => $request->vacancy_id,
                'status' => $request->input('status'),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $requirement->translates()->create([
                    'language_id' => $language->id,
                    'title' => trim($translationData['title'] ?? ''),
                    'description' => trim($translationData['description'] ?? ''),
                ]);
            }

            return redirect()->back()
                ->with('success', 'Vacancy requirement created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating vacancy requirement: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VacancyRequirement $vacancyRequirement)
    {
        $vacancyRequirement->load(['vacancy', 'translates.language']);
        $vacancies = Vacancy::all();
        $languages = Language::all();
        
        return view('admin.vacancy-requirements.edit', compact('vacancyRequirement', 'vacancies', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VacancyRequirement $vacancyRequirement)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'status' => 'boolean',
        ], [
            'vacancy_id.required' => 'Vacancy is required.',
            'vacancy_id.exists' => 'Selected vacancy does not exist.',
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

        try {
            // Update requirement
            $vacancyRequirement->update([
                'vacancy_id' => $request->vacancy_id,
                'status' => $request->input('status'),
            ]);

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $vacancyRequirement->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                        'description' => trim($translationData['description'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.vacancy-requirements.index')
                ->with('success', 'Vacancy requirement updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating vacancy requirement: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VacancyRequirement $vacancyRequirement)
    {
        try {
            $vacancyRequirement->delete();
            return redirect()->route('admin.vacancy-requirements.index')
                ->with('success', 'Vacancy requirement deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting vacancy requirement: ' . $e->getMessage());
        }
    }

    /**
     * Reorder requirements
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'requirements' => 'required|array',
            'requirements.*' => 'required|integer|exists:vacancy_requirements,id',
        ]);

        try {
            foreach ($request->requirements as $index => $requirementId) {
                VacancyRequirement::where('id', $requirementId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
