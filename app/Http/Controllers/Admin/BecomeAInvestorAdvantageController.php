<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestorAdvantage;
use Illuminate\Http\Request;
use App\Repositories\BecomeAInvestorAdvantageRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\BecomeAInvestorAdvantage\StoreBecomeAInvestorAdvantageRequest;
use App\Http\Requests\Admin\BecomeAInvestorAdvantage\UpdateBecomeAInvestorAdvantageRequest;

class BecomeAInvestorAdvantageController extends Controller
{
    public function __construct(
        public BecomeAInvestorAdvantageRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $advantages = $this->repository->getAdvantages();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.become-a-investor-advantages.index', compact('advantages', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.become-a-investor-advantages.create', compact('languages'));
    }

    public function store(StoreBecomeAInvestorAdvantageRequest $request)
    {
        try {
            $this->repository->createAdvantage(
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('icon')
            );

            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', 'Advantage created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(BecomeAInvestorAdvantage $becomeAInvestorAdvantage)
    {
        $becomeAInvestorAdvantage->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.become-a-investor-advantages.edit', compact('becomeAInvestorAdvantage', 'languages'));
    }

    public function update(UpdateBecomeAInvestorAdvantageRequest $request, BecomeAInvestorAdvantage $becomeAInvestorAdvantage)
    {
        try {
            $this->repository->updateAdvantage(
                $becomeAInvestorAdvantage,
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('icon')
            );

            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', 'Advantage updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(BecomeAInvestorAdvantage $becomeAInvestorAdvantage)
    {
        try {
            $this->repository->deleteAdvantage($becomeAInvestorAdvantage);
            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', 'Advantage deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting advantage: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'advantages' => 'required|array',
            'advantages.*' => 'required|integer|exists:become_a_investor_advantages,id',
        ]);

        try {
            $this->repository->reorderAdvantages($request->advantages);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:become_a_investor_advantages,id'
        ]);

        try {
            $this->repository->deleteSelected($request->ids);
            return redirect()->route('admin.become-a-investor.edit')
                ->with('success', count($request->ids) . ' advantage(s) deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}
