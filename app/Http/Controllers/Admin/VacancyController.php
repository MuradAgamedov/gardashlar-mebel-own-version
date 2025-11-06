<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\Language;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vacancies = Vacancy::with(['translates.language', 'requirements.translates.language'])->ordered()->get();
        return view('admin.vacancies.index', compact('vacancies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.vacancies.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();

        // Base validation
        $request->validate([
            'last_apply_date' => 'nullable|date|after:today',
            'email' => 'required|email|max:255',
            'status' => 'boolean',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.position" => 'required|string|max:255',
                "translations.{$language->id}.experience" => 'required|string|max:255',
                "translations.{$language->id}.work_schedule" => 'required|string|max:1000',
                "translations.{$language->id}.salary" => 'nullable|string|max:255',
                "translations.{$language->id}.address" => 'required|string|max:500',
                "translations.{$language->id}.seo_title" => 'nullable|string|max:255',
                "translations.{$language->id}.seo_keywords" => 'nullable|string',
                "translations.{$language->id}.seo_description" => 'nullable|string',
            ]);
        }

        try {
            $vacancy = Vacancy::create([
                'last_apply_date' => $request->last_apply_date,
                'email' => trim($request->email),
                'status' => $request->input('status'),
            ]);

            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);

                $vacancy->translates()->create([
                    'language_id' => $language->id,
                    'title' => trim($translationData['title'] ?? ''),
                    'slug' => trim($translationData['slug'] ?? ''),
                    'position' => trim($translationData['position'] ?? ''),
                    'experience' => trim($translationData['experience'] ?? ''),
                    'work_schedule' => trim($translationData['work_schedule'] ?? ''),
                    'salary' => trim($translationData['salary'] ?? ''),
                    'address' => trim($translationData['address'] ?? ''),
                    'seo_title' => trim($translationData['seo_title'] ?? ''),
                    'seo_keywords' => trim($translationData['seo_keywords'] ?? ''),
                    'seo_description' => trim($translationData['seo_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.vacancies.index')
                ->with('success', 'Vacancy created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error creating vacancy: ' . $e->getMessage()])
                ->withInput();
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacancy $vacancy)
    {
        $vacancy->load('translates.language');
        $languages = Language::all();
        
        return view('admin.vacancies.edit', compact('vacancy', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Vacancy $vacancy)
    {
        $languages = Language::all();

        // Base validation
        $request->validate([
            'last_apply_date' => 'nullable|date',
            'email' => 'required|email|max:255',
            'status' => 'boolean',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.position" => 'required|string|max:255',
                "translations.{$language->id}.experience" => 'required|string|max:255',
                "translations.{$language->id}.work_schedule" => 'required|string|max:1000',
                "translations.{$language->id}.salary" => 'nullable|string|max:255',
                "translations.{$language->id}.address" => 'required|string|max:500',
                "translations.{$language->id}.seo_title" => 'nullable|string|max:255',
                "translations.{$language->id}.seo_keywords" => 'nullable|string',
                "translations.{$language->id}.seo_description" => 'nullable|string',
            ]);
        }

        try {
            $vacancy->update([
                'last_apply_date' => $request->last_apply_date,
                'email' => trim($request->email),
                'status' => $request->input('status'),
            ]);

            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);

                $vacancy->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                        'slug' => trim($translationData['slug'] ?? ''),
                        'position' => trim($translationData['position'] ?? ''),
                        'experience' => trim($translationData['experience'] ?? ''),
                        'work_schedule' => trim($translationData['work_schedule'] ?? ''),
                        'salary' => trim($translationData['salary'] ?? ''),
                        'address' => trim($translationData['address'] ?? ''),
                        'seo_title' => trim($translationData['seo_title'] ?? ''),
                        'seo_keywords' => trim($translationData['seo_keywords'] ?? ''),
                        'seo_description' => trim($translationData['seo_description'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.vacancies.index')
                ->with('success', 'Vacancy updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error updating vacancy: ' . $e->getMessage()])
                ->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacancy $vacancy)
    {
        try {
            $vacancy->delete();
            return redirect()->route('admin.vacancies.index')
                ->with('success', 'Vacancy deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting vacancy: ' . $e->getMessage());
        }
    }

    /**
     * Reorder vacancies
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'vacancies' => 'required|array',
            'vacancies.*' => 'required|integer|exists:vacancies,id',
        ]);

        try {
            foreach ($request->vacancies as $index => $vacancyId) {
                Vacancy::where('id', $vacancyId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
