<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Email\ReorderEmailRequest;

use App\Http\Requests\Admin\Email\StoreEmailRequest;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\Services\Admin\EmailServiceInterface;

class EmailController extends Controller
{
    public function __construct(protected EmailServiceInterface $emailService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emails = $this->emailService->getEmails();
        return view('admin.emails.index', compact('emails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.emails.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmailRequest $request)
    {
        $request->validated();

        try {
            Email::create([
                'email' => trim($request->email),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.emails.index')
                ->with('success', __('Email created successfully.'));

        } catch (\Exception $e) {
            Log::error('Error creating email: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', __('Error creating email. Please try again.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Email $email)
    {
        return view('admin.emails.edit', compact('email'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Email $email)
    {
        $request->validated();

        try {
            $email->update([
                'email' => trim($request->email),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.emails.index')
                ->with('success', __('Email updated successfully.'));

        } catch (\Exception $e) {
            Log::error('Error updating email: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', __('Error updating email. Please try again.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email $email)
    {
        try {
            $email->delete();
            return redirect()->route('admin.emails.index')
                ->with('success', __('Email deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Error deleting email: ' . $e->getMessage());
            return back()->with('error', __('Error deleting email. Please try again.'));
        }
    }

    /**
     * Reorder emails
     */
    public function reorder(ReorderEmailRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->items as $index => $id) {
                Email::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering emails: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Error reordering emails')], 500);
        }
    }
}
