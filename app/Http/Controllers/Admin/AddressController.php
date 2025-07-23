<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AddressType;
use App\Models\AddressTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addressTypes = AddressType::with(['translates.language', 'addresses.translates.language'])
            ->ordered()
            ->get();

        return view('admin.addresses.index', compact('addressTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        $addressTypes = AddressType::active()->ordered()->get();
        
        return view('admin.addresses.create', compact('languages', 'addressTypes'));
    }

    /**
     * Show the form for creating a new address for a specific type.
     */
    public function createForType(AddressType $addressType)
    {
        $languages = Language::all();
        
        return view('admin.addresses.create-for-type', compact('languages', 'addressType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Address store request data:', $request->all());
        
        $request->validate([
            'address_type_id' => 'required|exists:address_types,id',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.address' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $address = Address::create([
                'address_type_id' => $request->address_type_id,
                'status' => $request->input('status', false),
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['address'])) {
                    AddressTranslate::create([
                        'address_id' => $address->id,
                        'language_id' => $languageId,
                        'address' => trim($translation['address']),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating address: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating address. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        $languages = Language::all();
        $addressTypes = AddressType::active()->ordered()->get();
        $address->load('translates.language');
        
        return view('admin.addresses.edit', compact('address', 'languages', 'addressTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        $request->validate([
            'address_type_id' => 'required|exists:address_types,id',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.address' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $address->update([
                'address_type_id' => $request->address_type_id,
                'status' => $request->input('status', false),
            ]);

            // Update translations
            foreach ($request->translations as $languageId => $translation) {
                if (!empty($translation['address'])) {
                    AddressTranslate::updateOrCreate(
                        [
                            'address_id' => $address->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'address' => trim($translation['address']),
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating address: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating address. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        try {
            $address->delete();
            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting address: ' . $e->getMessage());
            return back()->with('error', 'Error deleting address. Please try again.');
        }
    }

    /**
     * Reorder addresses
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:addresses,id'
        ]);

        try {
            foreach ($request->items as $index => $id) {
                Address::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering addresses: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering addresses'], 500);
        }
    }
}
