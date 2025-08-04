<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Administration;
use App\Models\AdministrationTranslate;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministrationController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $administrations = Administration::with('translates.language')->ordered()->get();
        return view('admin.administrations.index', compact('administrations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.administrations.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.position' => 'required|string|max:255',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Upload image
            $imagePath = $this->imageService->upload($request->file('image'), 'administrations');

            // Create administration
            $administration = Administration::create([
                'image' => $imagePath,
                'status' => $request->input('status', true)
            ]);

            // Create translates
            foreach ($request->input('translates') as $translate) {
                AdministrationTranslate::create([
                    'administration_id' => $administration->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'position' => $translate['position']
                ]);
            }

            DB::commit();

            return redirect()->route('admin.administrations.index')
                ->with('success', 'Administration member created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating administration member: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Administration $administration)
    {
        $administration->load('translates');
        $languages = Language::getActiveLanguages();
        return view('admin.administrations.edit', compact('administration', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administration $administration)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.position' => 'required|string|max:255',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($administration->image) {
                    $this->imageService->delete($administration->image);
                }
                // Upload new image
                $imagePath = $this->imageService->upload($request->file('image'), 'administrations');
                $administration->image = $imagePath;
            }

            $administration->status = $request->input('status', true);
            $administration->save();

            // Update translates
            foreach ($request->input('translates') as $translate) {
                AdministrationTranslate::updateOrCreate(
                    [
                        'administration_id' => $administration->id,
                        'language_id' => $translate['language_id']
                    ],
                    [
                        'title' => $translate['title'],
                        'position' => $translate['position']
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.administrations.index')
                ->with('success', 'Administration member updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating administration member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administration $administration)
    {
        try {
            // Delete image
            if ($administration->image) {
                $this->imageService->delete($administration->image);
            }
            
            $administration->delete();
            
            return redirect()->route('admin.administrations.index')
                ->with('success', 'Administration member deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting administration member: ' . $e->getMessage());
        }
    }

    /**
     * Reorder administration items
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:administrations,id'
        ]);

        try {
            foreach ($request->input('order') as $index => $id) {
                Administration::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 