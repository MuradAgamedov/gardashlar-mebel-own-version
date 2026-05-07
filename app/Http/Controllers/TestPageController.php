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
            
            try {
                // Send file to external API
                $response = \Http::asMultipart()
                    ->attach('file', file->get(), $file->getClientOriginalName())
                    ->post('https://front.stagnest.dev/api/import-redirect');

                if ($response->successful()) {
                    return back()->with('success', 'File sent successfully to external API');
                } else {
                    return back()->with('error', 'Failed to send file to external API: ' . $response->status());
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Error sending file: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'No file uploaded');
    }
}
