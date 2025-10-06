<?php
use App\Http\Controllers\Admin\CurrencyController;

Route::resource('currencies', CurrencyController::class);
Route::post('currencies/reorder', [CurrencyController::class, 'reorder'])->name('currencies.reorder');
Route::post('currencies/{currency}/toggle-status', [CurrencyController::class, 'toggleStatus'])->name('currencies.toggle-status');
Route::post('currencies/bulk-delete', [CurrencyController::class, 'bulkDelete'])->name('currencies.bulk-delete');
Route::post('currencies/bulk-toggle-status', [CurrencyController::class, 'bulkToggleStatus'])->name('currencies.bulk-toggle-status');