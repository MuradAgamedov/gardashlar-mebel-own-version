<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AddressType\ReorderAddressTypeRequest;

use App\Http\Controllers\Controller;
use App\Models\AddressType;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\AddressTypeServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\AddressType\StoreAddressTypeRequest;
use App\Http\Requests\Admin\AddressType\UpdateAddressTypeRequest;

class AddressTypeController extends Controller
{
    public function __construct(
        protected AddressTypeServiceInterface $addressTypeService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $addressTypes = $this->addressTypeService->getAddressTypes();
        return view('admin.address-types.index', compact('addressTypes'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.address-types.create', compact('languages'));
    }

    public function store(StoreAddressTypeRequest $request)
    {
        try {
            $this->addressTypeService->createAddressType(
                ['status' => $request->input('status', false)],
                $request->translations
            );

            return redirect()->route('admin.addresses.index')
                ->with('success', __('Address type created successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating address type. Please try again.'));
        }
    }

    public function edit(AddressType $addressType)
    {
        $languages = $this->languageService->getAllLanguages();
        $addressType->load('translates.language');
        return view('admin.address-types.edit', compact('addressType', 'languages'));
    }

    public function update(UpdateAddressTypeRequest $request, AddressType $addressType)
    {
        try {
            $this->addressTypeService->updateAddressType(
                $addressType,
                ['status' => $request->input('status', false)],
                $request->translations
            );

            return redirect()->route('admin.addresses.index')
                ->with('success', __('Address type updated successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating address type. Please try again.'));
        }
    }

    public function destroy(AddressType $addressType)
    {
        try {
            $this->addressTypeService->deleteAddressType($addressType);
            return redirect()->route('admin.addresses.index')
                ->with('success', __('Address type deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting address type. Please try again.'));
        }
    }

    public function reorder(ReorderAddressTypeRequest $request)
    {
        $request->validated();

        try {
            $this->addressTypeService->reorderAddressTypes($request->items);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Error reordering address types')], 500);
        }
    }
}
