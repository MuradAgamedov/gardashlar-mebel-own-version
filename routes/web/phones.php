<?php
use App\Http\Controllers\Admin\PhoneTypeController;
use App\Http\Controllers\Admin\PhoneController;

Route::resource('phone-types', PhoneTypeController::class);
Route::post('phone-types/reorder', [PhoneTypeController::class, 'reorder'])->name('phone-types.reorder');


Route::resource('phones', PhoneController::class);
Route::post('phones/reorder', [PhoneController::class, 'reorder'])->name('phones.reorder');
Route::get('phone-types/{type}/phones/create', [PhoneController::class, 'createForType'])->name('phone-types.phones.create');