<?php

use App\Http\Controllers\Admin\ReportController;
Route::resource('reports', ReportController::class);
Route::post('reports/reorder', [ReportController::class, 'reorder'])->name('reports.reorder');