<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurGoal;
use App\Models\OurGoalTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\OurGoalRepository;
use App\Repositories\LanguageRepository;

class OurGoalController extends Controller
{
    public function __construct(
        public OurGoalRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ourGoals = $this->repository->getOurGoals();
        return view('admin.our-goals.index', compact('ourGoals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.our-goals.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ]);

        try {
            $ourGoal = OurGoal::create([
                'status' => $request->input('status', false),
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    OurGoalTranslate::create([
                        'our_goal_id' => $ourGoal->id,
                        'language_id' => $languageId,
                        'title' => $translation['title'],
                        'description' => $translation['description'],
                    ]);
                }
            }

            return redirect()->route('admin.our-goals.index')
                ->with('success', 'Our Goal created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating our goal: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating our goal. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OurGoal $ourGoal)
    {
        $languages = $this->languageRepository->getAllLanguages();
        $translations = $ourGoal->translates->keyBy('language_id');
        return view('admin.our-goals.edit', compact('ourGoal', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OurGoal $ourGoal)
    {
        $request->validate([
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ]);

        try {
            $ourGoal->update([
                'status' => $request->input('status', false),
            ]);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    OurGoalTranslate::updateOrCreate(
                        [
                            'our_goal_id' => $ourGoal->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'title' => $translation['title'],
                            'description' => $translation['description'],
                        ]
                    );
                }
            }

            return redirect()->route('admin.our-goals.index')
                ->with('success', 'Our Goal updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating our goal: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating our goal. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OurGoal $ourGoal)
    {
        try {
            $ourGoal->delete();
            return redirect()->route('admin.our-goals.index')
                ->with('success', 'Our Goal deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting our goal: ' . $e->getMessage());
            return back()->with('error', 'Error deleting our goal. Please try again.');
        }
    }

    /**
     * Reorder our goals
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:our_goals,id'
        ]);

        try {
            foreach ($request->items as $index => $id) {
                OurGoal::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering our goals: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering our goals'], 500);
        }
    }
}
