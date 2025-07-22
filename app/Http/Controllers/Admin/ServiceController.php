<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::with('translates.language')->orderBy('order')->get();
        $languages = Language::active()->orderBy('order')->get();
        
        return view('admin.services.index', compact('services', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.services.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.card_description' => 'required|string',
            'translates.*.main_description' => 'required|string',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.card_description.required' => 'Card description is required for all languages.',
            'translates.*.main_description.required' => 'Main description is required for all languages.',
        ]);

        try {
            // Create the service
            $service = Service::create([
                'status' => $request->has('status') ? true : false
            ]);

            // Create translations for each language
            foreach ($request->translates as $translate) {
                $service->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'card_description' => trim($translate['card_description']),
                    'main_description' => trim($translate['main_description']),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('success', 'Service created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating service: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::with(['translates.language'])->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::with(['translates.language'])->findOrFail($id);
        $languages = Language::active()->orderBy('order')->get();
        return view('admin.services.edit', compact('service', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        // Validate the request
        $request->validate([
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.card_description' => 'required|string',
            'translates.*.main_description' => 'required|string',
            'translates.*.seo_title' => 'nullable|string|max:255',
            'translates.*.seo_keywords' => 'nullable|string',
            'translates.*.seo_description' => 'nullable|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.card_description.required' => 'Card description is required for all languages.',
            'translates.*.main_description.required' => 'Main description is required for all languages.',
        ]);

        try {
            // Update the service
            $service->update([
                'status' => $request->has('status') ? true : false
            ]);

            // Delete existing translations and create new ones
            $service->translates()->delete();

            // Create new translations
            foreach ($request->translates as $translate) {
                $service->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'card_description' => trim($translate['card_description']),
                    'main_description' => trim($translate['main_description']),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('success', 'Service updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating service: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting service: ' . $e->getMessage());
        }
    }

    /**
     * Update the order of services
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.order' => 'required|integer|min:0'
        ]);

        try {
            foreach ($request->services as $serviceData) {
                Service::where('id', $serviceData['id'])
                    ->update(['order' => $serviceData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->update(['status' => $request->status]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 