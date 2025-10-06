<?php
use App\Http\Controllers\Admin\AdministrationController;

Route::resource('administrations', AdministrationController::class);
Route::post('administrations/reorder', [AdministrationController::class, 'reorder'])->name('administrations.reorder');