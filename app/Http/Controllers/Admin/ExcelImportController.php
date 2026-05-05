<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExcelImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExcelImportController extends Controller
{
    /**
     * Display the Excel import edit page.
     */
    public function edit()
    {
        dd(1);
        $excelImport = ExcelImport::first();
        return view('admin.excel-imports.edit', compact('excelImport'));
    }

    /**
     * Update the Excel import.
     */
    public function update(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
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

            // Create or update record
            if ($excelImport) {
                $excelImport->update([
                    'file_url' => $filePath,
                ]);
            } else {
                $excelImport = ExcelImport::create([
                    'file_url' => $filePath,
                ]);
            }

            return redirect()->route('admin.excel-imports.edit')
                ->with('success', 'Excel faylı uğurla yükləndi');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Fayl yüklənərkən xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel file.
     */
    public function download()
    {
        $excelImport = ExcelImport::first();
        
        if (!$excelImport || !$excelImport->file_url) {
            abort(404, 'Excel faylı tapılmadı');
        }

        $filePath = storage_path('app/public/' . $excelImport->file_url);
        
        if (!file_exists($filePath)) {
            abort(404, 'Fayl mövcud deyil');
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
            ->with('success', 'Excel faylı uğurla silindi');
    }
}
