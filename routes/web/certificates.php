<?php
use App\Http\Controllers\Admin\CertificateController;

Route::resource('certificates', CertificateController::class);
Route::post('certificates/reorder', [CertificateController::class, 'reorder'])->name('certificates.reorder');