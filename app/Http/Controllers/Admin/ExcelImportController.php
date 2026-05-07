<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExcelImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExcelImportController extends Controller
{
    /**
     * Display the Excel import edit page.
     */
    public function edit()
    {
        $excelImport = ExcelImport::getOrCreateSingle();
        return view('admin.excel-imports.edit', compact('excelImport'));
    }

    /**
     * Update the Excel import.
     */
    public function update(Request $request)
    {
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240', // 10MB max
                function ($attribute, $value, $fail) {
                    // Additional validation for Excel file MIME types
                    $allowedMimeTypes = [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/vnd.ms-excel', // .xls
                        'application/octet-stream' // Sometimes Excel files are detected as this
                    ];
                    
                    if ($value && !in_array($value->getMimeType(), $allowedMimeTypes)) {
                        $fail(__('excel_file_type_error'));
                    }
                },
            ],
        ], [
            'excel_file.required' => __('excel_file_required'),
            'excel_file.file' => __('excel_file_file'),
            'excel_file.mimes' => __('excel_file_mimes'),
            'excel_file.max' => __('excel_file_max'),
        ]);

        try {
            // Delete old file if exists
            $excelImport = ExcelImport::first();
            if ($excelImport && $excelImport->file_url) {
                Storage::delete($excelImport->file_url);
            }

            // Upload new file
            $file = $request->file('excel_file');
            $filePath = $file->store('excel-imports', 'public');

            // Update the single record using model method
            $excelImport = ExcelImport::updateSingleFile($filePath);

            // Send file to external API
            $this->sendFileToExternalApi($file);

            return redirect()->route('admin.excel-imports.edit')
                ->with('success', __('excel_file_uploaded_success'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('excel_upload_error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Download the Excel file.
     */
    public function download()
    {
        $excelImport = ExcelImport::first();
        
        if (!$excelImport || !$excelImport->file_url) {
            abort(404, __('excel_file_not_found'));
        }

        $filePath = storage_path('app/public/' . $excelImport->file_url);
        
        if (!file_exists($filePath)) {
            abort(404, __('file_not_exists'));
        }

        return response()->download($filePath, 'excel-import.xlsx');
    }

    /**
     * Delete the Excel file.
     */
    public function destroy()
    {
        $excelImport = ExcelImport::first();
        
        if ($excelImport) {
            // Delete file from storage
            if ($excelImport->file_url) {
                Storage::delete($excelImport->file_url);
            }
            
            // Delete record
            $excelImport->delete();
        }

        return redirect()->route('admin.excel-imports.edit')
            ->with('success', __('excel_file_deleted_success'));
    }

    /**
     * Send file to external API.
     */
    private function sendFileToExternalApi($file)
    {
        try {
            $frontApiUrl = env('FRONT_API', 'https://front.stagnest.dev') . '/api/import-redirect';
            
            $fileContent = file_get_contents($file->getPathname());
            $fileName = $file->getClientOriginalName();
            
           $response = \Http::withHeaders([
                 "Origin" => env("APP_URL")   
                ])->asMultipart()
                    ->attach('file', $file->get(), $file->getClientOriginalName())
                    ->post($frontApiUrl);
            
            if ($response->successful()) {
                Log::info('Excel file successfully sent to external API', [
                    'file' => $fileName,
                    'api_url' => $frontApiUrl,
                    'response' => $response->body()
                ]);
            } else {
                Log::error('Failed to send Excel file to external API', [
                    'file' => $fileName,
                    'api_url' => $frontApiUrl,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception while sending Excel file to external API', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
        }
    }

    /**
     * API endpoint to get Excel file.
     */
    public function apiGetExcel()
    {
        $excelImport = ExcelImport::first();
        
        if (!$excelImport || !$excelImport->file_url) {
            return response()->json([
                'success' => false,
                'message' => __('excel_file_not_found')
            ], 404);
        }

        $filePath = storage_path('app/public/' . $excelImport->file_url);
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => __('file_not_exists')
            ], 404);
        }

        return response()->download($filePath, 'excel-import.xlsx');
    }
}
