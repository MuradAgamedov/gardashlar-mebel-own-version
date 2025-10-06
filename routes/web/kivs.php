<?php

use App\Http\Controllers\Admin\KivCategoryController;
use App\Http\Controllers\Admin\KivController;

Route::resource('kiv-categories', KivCategoryController::class);
Route::post('kiv-categories/reorder', [KivCategoryController::class, 'reorder'])->name('kiv-categories.reorder');

Route::resource('kivs', KivController::class);
Route::post('kivs/reorder', [KivController::class, 'reorder'])->name('kivs.reorder');