<?php

use App\Http\Controllers\Admin\ExcelImportController;
use Illuminate\Support\Facades\Route;

Route::resource('excel-imports', ExcelImportController::class)->only([
    'edit', 'update', 'destroy'
]);

Route::get('excel-imports/download', [ExcelImportController::class, 'download'])->name('excel-imports.download');
