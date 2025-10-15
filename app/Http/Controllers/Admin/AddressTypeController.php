<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddressType;
use Illuminate\Http\Request;
use App\Repositories\AddressTypeRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\AddressType\StoreAddressTypeRequest;
use App\Http\Requests\Admin\AddressType\UpdateAddressTypeRequest;

class AddressTypeController extends Controller
{
    public function __construct(
        public AddressTypeRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $addressTypes = $this->repository->getAddressTypes();
        return view('admin.address-types.index', compact('addressTypes'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.address-types.create', compact('languages'));
    }

    public function store(StoreAddressTypeRequest $request)
    {
        try {
            $this->repository->createAddressType(
                ['status' => $request->input('status', false)],
                $request->translations
            );

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address type created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating address type. Please try again.');
        }
    }

    public function edit(AddressType $addressType)
    {
        $languages = $this->languageRepository->getAllLanguages();
        $addressType->load('translates.language');
        return view('admin.address-types.edit', compact('addressType', 'languages'));
    }

    public function update(UpdateAddressTypeRequest $request, AddressType $addressType)
    {
        try {
            $this->repository->updateAddressType(
                $addressType,
                ['status' => $request->input('status', false)],
                $request->translations
            );

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address type updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating address type. Please try again.');
        }
    }

    public function destroy(AddressType $addressType)
    {
        try {
            $this->repository->deleteAddressType($addressType);
            return redirect()->route('admin.addresses.index')
                ->with('success', 'Address type deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting address type. Please try again.');
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:address_types,id'
        ]);

        try {
            $this->repository->reorderAddressTypes($request->items);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error reordering address types'], 500);
        }
    }
}
