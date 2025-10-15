<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurGoal;
use Illuminate\Http\Request;
use App\Repositories\OurGoalRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\OurGoal\StoreOurGoalRequest;
use App\Http\Requests\Admin\OurGoal\UpdateOurGoalRequest;

class OurGoalController extends Controller
{
    public function __construct(
        public OurGoalRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $ourGoals = $this->repository->getOurGoals();
        return view('admin.our-goals.index', compact('ourGoals'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.our-goals.create', compact('languages'));
    }

    public function store(StoreOurGoalRequest $request)
    {
        try {
            $this->repository->createOurGoal(
                ['status' => $request->input('status', false)],
                $request->translations
            );

            return redirect()->route('admin.our-goals.index')
                ->with('success', 'Our Goal created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating our goal. Please try again.');
        }
    }

    public function edit(OurGoal $ourGoal)
    {
        $languages = $this->languageRepository->getAllLanguages();
        $translations = $ourGoal->translates->keyBy('language_id');
        return view('admin.our-goals.edit', compact('ourGoal', 'languages', 'translations'));
    }

    public function update(UpdateOurGoalRequest $request, OurGoal $ourGoal)
    {
        try {
            $this->repository->updateOurGoal(
                $ourGoal,
                ['status' => $request->input('status', false)],
                $request->translations
            );

            return redirect()->route('admin.our-goals.index')
                ->with('success', 'Our Goal updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating our goal. Please try again.');
        }
    }

    public function destroy(OurGoal $ourGoal)
    {
        try {
            $this->repository->deleteOurGoal($ourGoal);
            return redirect()->route('admin.our-goals.index')
                ->with('success', 'Our Goal deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting our goal. Please try again.');
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:our_goals,id'
        ]);

        try {
            $this->repository->reorderOurGoals($request->items);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error reordering our goals'], 500);
        }
    }
}
