<?php

use App\Http\Controllers\Admin\ExcelImportController;
use Illuminate\Support\Facades\Route;

Route::get('excel-imports/edit', [ExcelImportController::class, 'edit'])->name('excel-imports.edit');
Route::put('excel-imports/update', [ExcelImportController::class, 'update'])->name('excel-imports.update');
Route::delete('excel-imports/destroy', [ExcelImportController::class, 'destroy'])->name('excel-imports.destroy');
Route::get('excel-imports/download', [ExcelImportController::class, 'download'])->name('excel-imports.download');
