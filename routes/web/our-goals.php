<?php


use App\Http\Controllers\Admin\OurGoalController;

Route::resource('our-goals', OurGoalController::class);
Route::post('our-goals/reorder', [OurGoalController::class, 'reorder'])->name('our-goals.reorder');