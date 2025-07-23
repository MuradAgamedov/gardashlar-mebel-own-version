<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emails = Email::ordered()->get();
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
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'status' => 'boolean',
        ]);

        try {
            Email::create([
                'email' => trim($request->email),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.emails.index')
                ->with('success', 'Email created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating email: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error creating email. Please try again.');
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
        $request->validate([
            'email' => 'required|email|max:255',
            'status' => 'boolean',
        ]);

        try {
            $email->update([
                'email' => trim($request->email),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.emails.index')
                ->with('success', 'Email updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating email: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating email. Please try again.');
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
                ->with('success', 'Email deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting email: ' . $e->getMessage());
            return back()->with('error', 'Error deleting email. Please try again.');
        }
    }

    /**
     * Reorder emails
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:emails,id'
        ]);

        try {
            foreach ($request->items as $index => $id) {
                Email::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering emails: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error reordering emails'], 500);
        }
    }
}
