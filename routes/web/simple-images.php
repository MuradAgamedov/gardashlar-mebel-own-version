<?php
use App\Http\Controllers\Admin\SimpleImageController;

Route::resource('simple-images', SimpleImageController::class);

// Quick upload routes
Route::get('simple-images/quick-upload/{keyword}', [SimpleImageController::class, 'quickUpload'])->name('simple-images.quick-upload');
Route::post('simple-images/quick-upload/{keyword}', [SimpleImageController::class, 'quickUploadStore'])->name('simple-images.quick-upload.store');
