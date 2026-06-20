<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BecomeAPartnerAdvantage\DeleteSelectedBecomeAPartnerAdvantageRequest;

use App\Http\Requests\Admin\BecomeAPartnerAdvantage\ReorderBecomeAPartnerAdvantageRequest;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartnerAdvantage;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\BecomeAPartnerAdvantageServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\BecomeAPartnerAdvantage\StoreBecomeAPartnerAdvantageRequest;
use App\Http\Requests\Admin\BecomeAPartnerAdvantage\UpdateBecomeAPartnerAdvantageRequest;

class BecomeAPartnerAdvantageController extends Controller
{
    public function __construct(
        protected BecomeAPartnerAdvantageServiceInterface $becomeAPartnerAdvantageService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $advantages = $this->becomeAPartnerAdvantageService->getAdvantages();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.become-a-partner-advantages.index', compact('advantages', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.become-a-partner-advantages.create', compact('languages'));
    }

    public function store(StoreBecomeAPartnerAdvantageRequest $request)
    {
        try {
            $this->becomeAPartnerAdvantageService->createAdvantage(
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('icon')
            );

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Advantage created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(BecomeAPartnerAdvantage $becomeAPartnerAdvantage)
    {
        $becomeAPartnerAdvantage->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.become-a-partner-advantages.edit', compact('becomeAPartnerAdvantage', 'languages'));
    }

    public function update(UpdateBecomeAPartnerAdvantageRequest $request, BecomeAPartnerAdvantage $becomeAPartnerAdvantage)
    {
        try {
            $this->becomeAPartnerAdvantageService->updateAdvantage(
                $becomeAPartnerAdvantage,
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('icon')
            );

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Advantage updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(BecomeAPartnerAdvantage $becomeAPartnerAdvantage)
    {
        try {
            $this->becomeAPartnerAdvantageService->deleteAdvantage($becomeAPartnerAdvantage);
            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Advantage deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting advantage: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderBecomeAPartnerAdvantageRequest $request)
    {
        $request->validated();

        try {
            $this->becomeAPartnerAdvantageService->reorderAdvantages($request->advantages);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(DeleteSelectedBecomeAPartnerAdvantageRequest $request)
    {
        $request->validated();

        try {
            $this->becomeAPartnerAdvantageService->deleteSelected($request->ids);
            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', count($request->ids) . ' advantage(s) deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}
