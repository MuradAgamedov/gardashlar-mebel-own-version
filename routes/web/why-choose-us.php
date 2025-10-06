<?php
use App\Http\Controllers\Admin\WhyChooseUsController;
use App\Http\Controllers\Admin\WhyChooseUsSliderController;
Route::get('why-choose-us/edit', [WhyChooseUsController::class, 'edit'])->name('why-choose-us.edit');
Route::put('why-choose-us/update', [WhyChooseUsController::class, 'update'])->name('why-choose-us.update');
Route::resource('why-choose-us-sliders', WhyChooseUsSliderController::class);
Route::post('why-choose-us-sliders/reorder', [WhyChooseUsSliderController::class, 'reorder'])->name('why-choose-us-sliders.reorder');