<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Service\DeleteSelectedServiceRequest;

use App\Http\Requests\Admin\Service\UpdateOrderServiceRequest;

use App\Http\Requests\Admin\Service\StoreServiceRequest;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\ServiceServiceInterface;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceServiceInterface $serviceService,
        protected LanguageServiceInterface $languageService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = $this->serviceService->getServices($request);
        $languages = $this->languageService->getActiveLanguages();
        
        return view('admin.services.index', compact('services', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.services.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $request->validated();

        try {
            $this->serviceService->createService(
                ['status' => $request->status == '1'],
                $request->translates
            );

            return redirect()->route('admin.services.index')
                ->with('success', __('Service created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating service: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = $this->serviceService->findOrFail((int)$id, ['translates.language']);
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = $this->serviceService->findOrFail((int)$id, ['translates.language']);
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.services.edit', compact('service', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validated();

        try {
            $this->serviceService->updateService(
                (int)$id,
                ['status' => $request->status == '1'],
                $request->translates
            );

            return redirect()->route('admin.services.index')
                ->with('success', __('Service updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating service: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->serviceService->delete((int)$id);

            return redirect()->route('admin.services.index')
                ->with('success', __('Service deleted successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting service: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the order of services
     */
    public function updateOrder(UpdateOrderServiceRequest $request)
    {
        $request->validated();

        try {
            $this->serviceService->updateServiceOrder($request->services);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(Request $request, Service $service)
    {
        try {
            $status = $request->boolean('status');
            $this->serviceService->update($service->id, ['status' => $status ? 1 : 0]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete selected services.
     */
    public function deleteSelected(DeleteSelectedServiceRequest $request)
    {
        $request->validated();

        try {
            $this->serviceService->bulkDelete($request->ids);

            return redirect()->route('admin.services.index')
                ->with('success', __(':count service(s) deleted successfully.', ['count' => count($request->ids)]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}