<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ServiceAdvantageRepository;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceAdvantageController extends Controller
{
    protected $imageService;
    protected $repository;
{{ ... }}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $serviceId)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
{{ ... }}
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.description.required' => 'Description is required for all languages.',
        ]);

        try {
            Log::info('Service Advantage creation started', [
                'service_id' => $request->service_id,
                'request_data' => $request->all()
            ]);

            $data = [
                'service_id' => $request->service_id,
                'status' => $request->has('status')
            ];

            $advantage = $this->repository->createAdvantage(
                $data,
                $request->translates,
                $request->file('icon')
            );

            Log::info('ServiceAdvantage creation completed successfully', [
                'advantage_id' => $advantage->id
            ]);

            return redirect()
                ->route('admin.service-advantages.index', $serviceId)
                ->with('success', 'Service advantage created successfully!');

        } catch (\Exception $e) {
            Log::error('ServiceAdvantage creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Error creating service advantage: ' . $e->getMessage())
                ->withErrors(['general' => $e->getMessage()])
                ->withInput();
        }
    }
{{ ... }}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $serviceId, $advantageId)
    {
        $request->validate([
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.icon_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.description.required' => 'Description is required for all languages.',
        ]);

        try {
            $data = [
                'status' => $request->has('status')
            ];

            $this->repository->updateAdvantage(
                $advantageId,
                $data,
                $request->translates,
                $request->file('icon')
            );

            return redirect()
                ->route('admin.service-advantages.index', $serviceId)
                ->with('success', 'Service advantage updated successfully!');

        } catch (\Exception $e) {
            Log::error('ServiceAdvantage update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Error updating service advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($serviceId, $advantageId)
    {
        try {
            $this->repository->deleteAdvantage($advantageId);

            return redirect()
                ->route('admin.service-advantages.index', $serviceId)
                ->with('success', 'Service advantage deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Service Advantage deletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error deleting service advantage: ' . $e->getMessage());
        }
    }

    /**
{{ ... }}
            'advantages.*.id' => 'required|exists:service_advantages,id',
            'advantages.*.order' => 'required|integer|min:0'
        ]);

        try {
            $this->repository->updateOrder($request->advantages, $serviceId);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Service Advantage order update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle advantage status
     */
    public function toggleStatus(Request $request, $serviceId, $advantageId)
    {
        try {
            $this->repository->toggleStatus($advantageId);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('ServiceAdvantage status toggle failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to toggle status: ' . $e->getMessage()
            ], 500);
        }
    }