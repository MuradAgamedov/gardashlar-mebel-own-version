<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Address\ReorderAddressRequest;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AddressType;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\AddressServiceInterface;
use App\Http\Requests\Admin\Address\StoreAddressRequest;
use App\Http\Requests\Admin\Address\UpdateAddressRequest;

class AddressController extends Controller
{

    public function __construct(protected LanguageServiceInterface $languageService, protected AddressServiceInterface $addressService)
    {
    }

    public function index()
    {
        $addressTypes = $this->addressService->getAddresses();
        return view('admin.addresses.index', compact('addressTypes'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        $addressTypes = AddressType::active()->ordered()->get();
        return view('admin.addresses.create', compact('languages', 'addressTypes'));
    }

    public function createForType(AddressType $addressType)
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.addresses.create-for-type', compact('languages', 'addressType'));
    }

    public function store(StoreAddressRequest $request)
    {
        try {
            $this->addressService->createAddress(
                [
                    'address_type_id' => $request->address_type_id,
                    'status' => $request->input('status', false),
                ],
                $request->translations
            );

            return redirect()->route('admin.addresses.index')
                ->with('success', __('Address created successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating address. Please try again.'));
        }
    }

    public function edit(Address $address)
    {
        $languages = $this->languageService->getAllLanguages();
        $addressTypes = AddressType::active()->ordered()->get();
        $address->load('translates.language');
        return view('admin.addresses.edit', compact('address', 'languages', 'addressTypes'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        try {
            $this->addressService->updateAddress(
                $address,
                [
                    'address_type_id' => $request->address_type_id,
                    'status' => $request->input('status', false),
                ],
                $request->translations
            );

            return redirect()->route('admin.addresses.index')
                ->with('success', __('Address updated successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating address. Please try again.'));
        }
    }

    public function destroy(Address $address)
    {
        try {
            $this->addressService->deleteAddress($address);
            return redirect()->route('admin.addresses.index')
                ->with('success', __('Address deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting address. Please try again.'));
        }
    }

    public function reorder(ReorderAddressRequest $request)
    {
        $request->validated();

        try {
            $this->addressService->reorderAddresses($request->items);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Error reordering addresses')], 500);
        }
    }
}
