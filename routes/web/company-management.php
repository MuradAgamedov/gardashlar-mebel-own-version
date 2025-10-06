<?php
use App\Http\Controllers\Admin\CompanyManagementController;

Route::get('company-management/edit', [CompanyManagementController::class, 'edit'])->name('company-management.edit');
Route::put('company-management/update', [CompanyManagementController::class, 'update'])->name('company-management.update');

Route::post('upload/image', [CompanyManagementController::class, 'uploadImage'])->name('upload.image');
