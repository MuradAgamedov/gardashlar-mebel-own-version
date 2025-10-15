<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Administration;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\AdministrationRepository;
use App\Http\Requests\Admin\Administration\StoreAdministrationRequest;
use App\Http\Requests\Admin\Administration\UpdateAdministrationRequest;

class AdministrationController extends Controller
{
    public function __construct(public AdministrationRepository $repository)
    {
    }

    public function index()
    {
        $administrations = $this->repository->getAdministrations();
        return view('admin.administrations.index', compact('administrations'));
    }

    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.administrations.create', compact('languages'));
    }

    public function store(StoreAdministrationRequest $request)
    {
        try {
            $this->repository->createAdministration(
                ['status' => $request->input('status', true)],
                $request->input('translates'),
                $request->file('image')
            );

            return redirect()->route('admin.administrations.index')
                ->with('success', 'Administration member created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating administration member: ' . $e->getMessage());
        }
    }

    public function edit(Administration $administration)
    {
        $administration->load('translates');
        $languages = Language::getActiveLanguages();
        return view('admin.administrations.edit', compact('administration', 'languages'));
    }

    public function update(UpdateAdministrationRequest $request, Administration $administration)
    {
        try {
            $this->repository->updateAdministration(
                $administration,
                ['status' => $request->input('status', true)],
                $request->input('translates'),
                $request->file('image')
            );

            return redirect()->route('admin.administrations.index')
                ->with('success', 'Administration member updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating administration member: ' . $e->getMessage());
        }
    }

    public function destroy(Administration $administration)
    {
        try {
            $this->repository->deleteAdministration($administration);
            
            return redirect()->route('admin.administrations.index')
                ->with('success', 'Administration member deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting administration member: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:administrations,id'
        ]);

        try {
            $this->repository->reorderAdministrations($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 