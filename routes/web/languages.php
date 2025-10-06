<?php
use App\Http\Controllers\Admin\LanguageController;
Route::resource('languages', LanguageController::class);
Route::post('languages/reorder', [LanguageController::class, 'reorder'])->name('languages.reorder');