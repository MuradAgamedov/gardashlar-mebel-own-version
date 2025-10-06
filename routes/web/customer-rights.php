<?php
use App\Http\Controllers\Admin\CustomerRightsController;

Route::get('customer-rights/edit', [CustomerRightsController::class, 'edit'])->name('customer-rights.edit');
Route::put('customer-rights/update', [CustomerRightsController::class, 'update'])->name('customer-rights.update');