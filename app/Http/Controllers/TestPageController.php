<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestPageController extends Controller
{
    public function index()
    {
        return view('test-page');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file_input' => 'required|file|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file_input')) {
            $file = $request->file('file_input');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            
            return back()->with('success', 'File uploaded successfully: ' . $fileName);
        }

        return back()->with('error', 'No file uploaded');
    }
}
