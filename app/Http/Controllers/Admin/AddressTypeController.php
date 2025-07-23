<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddressType;
use App\Models\AddressTypeTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addressTypes = AddressType::with(['translates.language'])
            ->ordered()
            ->get();

        return view('admin.address-types.index', compact('addressTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.address-types.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $addressType = AddressType::create([
                'status' => $request->input('status', false),
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    AddressTypeTranslate::create([
                        'address_type_id' => $addressType->id,
                        'language_id' => $languageId,
                        'title' => trim($translation['title']),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address type created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating address type: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating address type. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddressType $addressType)
    {
        $languages = Language::all();
        $addressType->load('translates.language');
        
        return view('admin.address-types.edit', compact('addressType', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AddressType $addressType)
    {
        $request->validate([
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $addressType->update([
                'status' => $request->input('status', false),
            ]);

            // Update translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    AddressTypeTranslate::updateOrCreate(
                        [
                            'address_type_id' => $addressType->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'title' => trim($translation['title']),
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address type updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating address type: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating address type. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddressType $addressType)
    {
        try {
            $addressType->delete();
            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting address type: ' . $e->getMessage());
            return back()->with('error', 'Error deleting address type. Please try again.');
        }
    }

    /**
     * Reorder address types
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:address_types,id'
        ]);

        try {
            foreach ($request->items as $index => $id) {
                AddressType::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering address types: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering address types'], 500);
        }
    }
}
