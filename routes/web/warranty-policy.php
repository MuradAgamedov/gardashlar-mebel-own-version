<?php

use App\Http\Controllers\Admin\WarrantyPolicyController;

Route::get('warranty-policy/edit', [WarrantyPolicyController::class, 'edit'])->name('warranty-policy.edit');
Route::put('warranty-policy/update', [WarrantyPolicyController::class, 'update'])->name('warranty-policy.update');