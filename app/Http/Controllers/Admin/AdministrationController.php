<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Administration\ReorderAdministrationRequest;

use App\Http\Controllers\Controller;
use App\Models\Administration;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\AdministrationServiceInterface;
use App\Http\Requests\Admin\Administration\StoreAdministrationRequest;
use App\Http\Requests\Admin\Administration\UpdateAdministrationRequest;

class AdministrationController extends Controller
{
    public function __construct(protected LanguageServiceInterface $languageService, protected AdministrationServiceInterface $administrationService)
    {
    }

    public function index()
    {
        $administrations = $this->administrationService->getAdministrations();
        return view('admin.administrations.index', compact('administrations'));
    }

    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.administrations.create', compact('languages'));
    }

    public function store(StoreAdministrationRequest $request)
    {
        try {
            $this->administrationService->createAdministration(
                ['status' => $request->input('status', true)],
                $request->input('translates'),
                $request->file('image')
            );

            return redirect()->route('admin.administrations.index')
                ->with('success', __('Administration member created successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error creating administration member: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function edit(Administration $administration)
    {
        $administration->load('translates');
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.administrations.edit', compact('administration', 'languages'));
    }

    public function update(UpdateAdministrationRequest $request, Administration $administration)
    {
        try {
            $this->administrationService->updateAdministration(
                $administration,
                ['status' => $request->input('status', true)],
                $request->input('translates'),
                $request->file('image')
            );

            return redirect()->route('admin.administrations.index')
                ->with('success', __('Administration member updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error updating administration member: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function destroy(Administration $administration)
    {
        try {
            $this->administrationService->deleteAdministration($administration);
            
            return redirect()->route('admin.administrations.index')
                ->with('success', __('Administration member deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting administration member: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function reorder(ReorderAdministrationRequest $request)
    {
        $request->validated();

        try {
            $this->administrationService->reorderAdministrations($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 