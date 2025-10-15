<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use Illuminate\Http\Request;
use App\Repositories\PhoneRepository;
use App\Http\Requests\Admin\Phone\StorePhoneRequest;
use App\Http\Requests\Admin\Phone\UpdatePhoneRequest;

class PhoneController extends Controller
{
    public function __construct(public PhoneRepository $repository)
    {
    }

    public function index()
    {
        $phones = $this->repository->getPhones();
        $phoneTypes = $this->repository->getPhoneTypes();
        return view('admin.phones.index', compact('phones', 'phoneTypes'));
    }

    public function create()
    {
        $phoneTypes = $this->repository->getActivePhoneTypes();
        return view('admin.phones.create', compact('phoneTypes'));
    }

    public function createForType($typeId)
    {
        $phoneType = $this->repository->findPhoneType($typeId);
        return view('admin.phones.create-for-type', compact('phoneType'));
    }

    public function store(StorePhoneRequest $request)
    {
        try {
            $this->repository->createPhone([
                'phone_type_id' => $request->input('phone_type_id'),
                'phone' => trim($request->input('phone')),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating phone: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Phone $phone)
    {
        $phone->load('phoneType.translates.language');
        $phoneTypes = $this->repository->getActivePhoneTypes();
        return view('admin.phones.edit', compact('phone', 'phoneTypes'));
    }

    public function update(UpdatePhoneRequest $request, Phone $phone)
    {
        try {
            $this->repository->updatePhone($phone, [
                'phone_type_id' => $request->input('phone_type_id'),
                'phone' => trim($request->input('phone')),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating phone: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Phone $phone)
    {
        try {
            $this->repository->deletePhone($phone);
            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting phone: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        try {
            $this->repository->reorderPhones($request->input('phones', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 