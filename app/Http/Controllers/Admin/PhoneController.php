<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\PhoneServiceInterface;
use App\Http\Requests\Admin\Phone\StorePhoneRequest;
use App\Http\Requests\Admin\Phone\UpdatePhoneRequest;

class PhoneController extends Controller
{
    public function __construct(protected PhoneServiceInterface $phoneService)
    {
    }

    public function index()
    {
        $phones = $this->phoneService->getPhones();
        $phoneTypes = $this->phoneService->getPhoneTypes();
        return view('admin.phones.index', compact('phones', 'phoneTypes'));
    }

    public function create()
    {
        $phoneTypes = $this->phoneService->getActivePhoneTypes();
        return view('admin.phones.create', compact('phoneTypes'));
    }

    public function createForType($typeId)
    {
        $phoneType = $this->phoneService->findPhoneType($typeId);
        return view('admin.phones.create-for-type', compact('phoneType'));
    }

    public function store(StorePhoneRequest $request)
    {
        try {
            $this->phoneService->createPhone([
                'phone_type_id' => $request->input('phone_type_id'),
                'phone' => trim($request->input('phone')),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.phones.index')
                ->with('success', __('Phone created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating phone: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function edit(Phone $phone)
    {
        $phone->load('phoneType.translates.language');
        $phoneTypes = $this->phoneService->getActivePhoneTypes();
        return view('admin.phones.edit', compact('phone', 'phoneTypes'));
    }

    public function update(UpdatePhoneRequest $request, Phone $phone)
    {
        try {
            $this->phoneService->updatePhone($phone, [
                'phone_type_id' => $request->input('phone_type_id'),
                'phone' => trim($request->input('phone')),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.phones.index')
                ->with('success', __('Phone updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating phone: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(Phone $phone)
    {
        try {
            $this->phoneService->deletePhone($phone);
            return redirect()->route('admin.phones.index')
                ->with('success', __('Phone deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting phone: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(Request $request)
    {
        try {
            $this->phoneService->reorderPhones($request->input('phones', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}