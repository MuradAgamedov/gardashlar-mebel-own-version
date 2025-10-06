<?php

use App\Http\Controllers\Admin\TestimonialController;

Route::resource('testimonials', TestimonialController::class);
Route::post('testimonials/reorder', [TestimonialController::class, 'reorder'])->name('testimonials.reorder');