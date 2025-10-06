<?php

use App\Http\Controllers\Admin\PartnerSliderController;


Route::resource('partner-sliders', PartnerSliderController::class);
Route::post('partner-sliders/reorder', [PartnerSliderController::class, 'reorder'])->name('partner-sliders.reorder');