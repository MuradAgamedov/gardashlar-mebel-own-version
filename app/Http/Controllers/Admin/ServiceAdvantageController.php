<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Service;
use App\Models\ServiceAdvantage;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ServiceAdvantageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($serviceId)
    {
        $service = Service::with(['translates.language'])->findOrFail($serviceId);
        $advantages = ServiceAdvantage::with(['translates.language'])
            ->where('service_id', $serviceId)
            ->orderBy('order')
            ->get();
        $languages = Language::active()->orderBy('order')->get();
        
        return view('admin.service-advantages.index', compact('service', 'advantages', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($serviceId)
    {
        $service = Service::with(['translates.language'])->findOrFail($serviceId);
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.service-advantages.create', compact('service', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $languages = Language::active()->orderBy('order')->get();
        // Base validation
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.icon_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
        ], [
            'service_id.required' => 'Service ID is required.',
            'service_id.exists' => 'Selected service does not exist.',
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.description.required' => 'Description is required for all languages.',
        ]);

        try {
            \Log::info('ServiceAdvantage creation started', [
                'service_id' => $request->service_id,
                'request_data' => $request->all()
            ]);

            // Handle icon upload
            $iconPath = null;
            if ($request->hasFile('icon')) {
                $iconPath = $this->imageService->upload($request->file('icon'), 'service-advantages');
                \Log::info('Icon uploaded', ['path' => $iconPath]);
            }

            // Create advantage
            $advantage = ServiceAdvantage::create([
                'service_id' => $request->service_id,
                'icon' => $iconPath,
                'status' => $request->has('status') ? true : false
            ]);

            \Log::info('ServiceAdvantage created', ['advantage_id' => $advantage->id]);

            // Create translations
            foreach ($request->translates as $translate) {
                $translation = $advantage->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'icon_alt_text' => trim($translate['icon_alt_text'] ?? ''),
                    'description' => trim($translate['description']),
                ]);
                \Log::info('Translation created', [
                    'translation_id' => $translation->id,
                    'language_id' => $translate['language_id']
                ]);
            }

            \Log::info('ServiceAdvantage creation completed successfully');

            return redirect()->route('admin.service-advantages.index', $serviceId)
                ->with('success', 'Service advantage created successfully!');

        } catch (\Exception $e) {
            \Log::error('ServiceAdvantage creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error creating service advantage: ' . $e->getMessage())
                ->withErrors(['general' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($serviceId, $advantageId)
    {
        $service = Service::with(['translates.language'])->findOrFail($serviceId);
        $advantage = ServiceAdvantage::with(['translates.language'])->findOrFail($advantageId);
        $languages = Language::active()->orderBy('order')->get();
        
        return view('admin.service-advantages.edit', compact('service', 'advantage', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $serviceId, $advantageId)
    {
        $service = Service::findOrFail($serviceId);
        $advantage = ServiceAdvantage::findOrFail($advantageId);
        $languages = Language::active()->orderBy('order')->get();

        // Base validation
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
            // Handle icon upload
            $iconPath = $advantage->icon;
            if ($request->hasFile('icon')) {
                $iconPath = $this->imageService->update($request->file('icon'), $advantage->icon, 'service-advantages');
            }

            // Update advantage
            $advantage->update([
                'icon' => $iconPath,
                'status' => $request->has('status') ? true : false
            ]);

            // Delete existing translations and create new ones
            $advantage->translates()->delete();

            // Create new translations
            foreach ($request->translates as $translate) {
                $advantage->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'icon_alt_text' => trim($translate['icon_alt_text'] ?? ''),
                    'description' => trim($translate['description']),
                ]);
            }

            return redirect()->route('admin.service-advantages.index', $serviceId)
                ->with('success', 'Service advantage updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
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
            $advantage = ServiceAdvantage::findOrFail($advantageId);
            $advantage->delete();

            return redirect()->route('admin.service-advantages.index', $serviceId)
                ->with('success', 'Service advantage deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting service advantage: ' . $e->getMessage());
        }
    }

    /**
     * Update the order of advantages
     */
    public function updateOrder(Request $request, $serviceId)
    {
        $request->validate([
            'advantages' => 'required|array',
            'advantages.*.id' => 'required|exists:service_advantages,id',
            'advantages.*.order' => 'required|integer|min:0'
        ]);

        try {
            foreach ($request->advantages as $advantageData) {
                ServiceAdvantage::where('id', $advantageData['id'])
                    ->where('service_id', $serviceId)
                    ->update(['order' => $advantageData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle advantage status
     */
    public function toggleStatus(Request $request, $serviceId, $advantageId)
    {
        try {
            $advantage = ServiceAdvantage::where('service_id', $serviceId)
                ->where('id', $advantageId)
                ->firstOrFail();
            
            $advantage->update(['status' => $request->status]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 