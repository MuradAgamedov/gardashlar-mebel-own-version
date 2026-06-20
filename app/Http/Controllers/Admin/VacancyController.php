<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Vacancy\ReorderVacancyRequest;

use App\Http\Requests\Admin\Vacancy\UpdateVacancyRequest;

use App\Http\Requests\Admin\Vacancy\StoreVacancyRequest;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

use App\Interfaces\Services\Admin\VacancyServiceInterface;

class VacancyController extends Controller
{
    public function __construct(
        protected VacancyServiceInterface $vacancyService,
        protected LanguageServiceInterface $languageService
    ) {
    }


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
        $languages = $this->languageService->getAllLanguages();
        return view('admin.vacancies.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(StoreVacancyStoreVacancyRequest $request)
{
    $languages = $this->languageService->getAllLanguages();

    // ✅ Base validation
    $request->validated();

    // ✅ Translation validation
    foreach ($languages as $language) {
        $request->validated();
    }

    try {
        // ✅ Create main vacancy
        $vacancy = Vacancy::create([
            'last_apply_date' => $request->last_apply_date,
            'email' => trim($request->email),
            'phone' => trim($request->phone),
            'age' => $request->age,
            'status' => $request->input('status'),
        ]);

        // ✅ Create translations
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
                'education' => trim($translationData['education'] ?? ''),
                'related_person' => trim($translationData['related_person'] ?? ''),
                'city' => trim($translationData['city'] ?? ''),
                'seo_title' => trim($translationData['seo_title'] ?? ''),
                'seo_keywords' => trim($translationData['seo_keywords'] ?? ''),
                'seo_description' => trim($translationData['seo_description'] ?? ''),
            ]);
        }

        return redirect()->route('admin.vacancies.index')
            ->with('success', __('Vacancy created successfully!'));

    } catch (\Exception $e) {
        return redirect()->back()
            ->withErrors(['error' => __('Error creating vacancy: :error', ['error' => $e->getMessage()])])
            ->withInput();
    }
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacancy $vacancy)
    {
        $vacancy->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.vacancies.edit', compact('vacancy', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(UpdateVacancyRequest $request, Vacancy $vacancy)
{
    $languages = $this->languageService->getAllLanguages();

    // ✅ Base validation
    $request->validated();

    // ✅ Translation validation
    foreach ($languages as $language) {
        $request->validated();
    }

    try {
        // ✅ Update main Vacancy
        $vacancy->update([
            'last_apply_date' => $request->last_apply_date,
            'email' => trim($request->email),
            'phone' => trim($request->phone),
            'age' => $request->age,
            'status' => $request->input('status'),
        ]);

        // ✅ Update translations
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
                    'education' => trim($translationData['education'] ?? ''),
                    'related_person' => trim($translationData['related_person'] ?? ''),
                    'city' => trim($translationData['city'] ?? ''),
                    'seo_title' => trim($translationData['seo_title'] ?? ''),
                    'seo_keywords' => trim($translationData['seo_keywords'] ?? ''),
                    'seo_description' => trim($translationData['seo_description'] ?? ''),
                ]
            );
        }

        return redirect()->route('admin.vacancies.index')
            ->with('success', __('Vacancy updated successfully!'));

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
                ->with('success', __('Vacancy deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting vacancy: ' . $e->getMessage());
        }
    }

    /**
     * Reorder vacancies
     */
    public function reorder(ReorderVacancyRequest $request)
    {
        $request->validated();

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
