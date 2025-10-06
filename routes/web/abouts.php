<?php
use App\Http\Controllers\Admin\AboutController;

Route::resource('abouts', AboutController::class);
Route::post('abouts/reorder', [AboutController::class, 'reorder'])->name('abouts.reorder');