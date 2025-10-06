<?php

use App\Http\Controllers\Admin\MapController;

Route::resource('maps', MapController::class);
Route::post('maps/reorder', [MapController::class, 'reorder'])->name('maps.reorder');