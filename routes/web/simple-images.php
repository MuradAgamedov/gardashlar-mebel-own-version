<?php
use App\Http\Controllers\Admin\SimpleImageController;

// Download route
Route::get('simple-images/download/{keyword}', [SimpleImageController::class, 'downloadImage'])->name('simple-images.download');
