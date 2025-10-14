<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\PhoneType;
use Illuminate\Http\Request;
use App\Repositories\PhoneRepository;

class PhoneController extends Controller
{
    public function __construct(public PhoneRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $phones = $this->repository->getPhones();
        $phoneTypes = $this->repository->getPhoneTypes();
        
        return view('admin.phones.index', compact('phones', 'phoneTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $phoneTypes = $this->repository->getActivePhoneTypes();
        return view('admin.phones.create', compact('phoneTypes'));
    }

    /**
     * Show the form for creating a new phone for a specific type.
     */
    public function createForType($typeId)
    {
        $phoneType = \App\Models\PhoneType::with('translates.language')->findOrFail($typeId);
        return view('admin.phones.create-for-type', compact('phoneType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Phone store request data: ' . json_encode($request->all()));
        
        $request->validate([
            'phone_type_id' => 'required|exists:phone_types,id',
            'phone' => 'required|string|max:255',
        ], [
            'phone_type_id.required' => 'Phone type is required.',
            'phone_type_id.exists' => 'Selected phone type does not exist.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number may not be greater than 255 characters.',
        ]);

        try {
            $phoneData = [
                'phone_type_id' => $request->input('phone_type_id'),
                'phone' => trim($request->input('phone')),
                'status' => $request->input('status', false),
            ];
            
            // Debug: Log the data being created
            \Log::info('Phone data to create: ' . json_encode($phoneData));
            
            $phone = Phone::create($phoneData);
            
            // Debug: Log the created phone
            \Log::info('Phone created successfully: ' . json_encode($phone));

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone created successfully!');

        } catch (\Exception $e) {
            \Log::error('Phone creation error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Error creating phone: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phone $phone)
    {
        $phone->load('phoneType.translates.language');
        $phoneTypes = $this->repository->getActivePhoneTypes();
        
        return view('admin.phones.edit', compact('phone', 'phoneTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phone $phone)
    {
        $request->validate([
            'phone_type_id' => 'required|exists:phone_types,id',
            'phone' => 'required|string|max:255',
        ], [
            'phone_type_id.required' => 'Phone type is required.',
            'phone_type_id.exists' => 'Selected phone type does not exist.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number may not be greater than 255 characters.',
        ]);

        try {
            $phone->update([
                'phone_type_id' => $request->input('phone_type_id'),
                'phone' => trim($request->input('phone')),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Phone update error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return redirect()->back()
                ->with('error', 'Error updating phone: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phone $phone)
    {
        try {
            $phone->delete();
            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Phone deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting phone: ' . $e->getMessage());
        }
    }

    /**
     * Reorder phones
     */
    public function reorder(Request $request)
    {
        try {
            $phones = $request->input('phones', []);
            
            foreach ($phones as $index => $phoneId) {
                Phone::where('id', $phoneId)->update(['order' => $index + 1]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Phone reorder error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 