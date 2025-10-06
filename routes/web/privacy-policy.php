<?php
use App\Http\Controllers\Admin\PrivacyPolicyController;

Route::get('privacy-policy/edit', [PrivacyPolicyController::class, 'edit'])->name('privacy-policy.edit');
Route::put('privacy-policy/update', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');