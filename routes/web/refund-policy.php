<?php
use App\Http\Controllers\Admin\RefundPolicyController;

Route::get('refund-policy/edit', [RefundPolicyController::class, 'edit'])->name('refund-policy.edit');
Route::put('refund-policy/update', [RefundPolicyController::class, 'update'])->name('refund-policy.update');