<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\WhyChooseUsController;
use App\Http\Controllers\Admin\WhyChooseUsSliderController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\BecomeAPartnerController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Language routes
    Route::resource('languages', LanguageController::class);
    Route::post('languages/reorder', [LanguageController::class, 'reorder'])->name('languages.reorder');
    
    // Page routes
    Route::resource('pages', PageController::class);
    Route::post('pages/reorder', [PageController::class, 'reorder'])->name('pages.reorder');
    Route::post('pages/generate-slug', [PageController::class, 'generateSlug'])->name('pages.generate-slug');
    
    // Why Choose Us routes
    Route::get('why-choose-us/edit', [WhyChooseUsController::class, 'edit'])->name('why-choose-us.edit');
    Route::put('why-choose-us/update', [WhyChooseUsController::class, 'update'])->name('why-choose-us.update');
    
    // Why Choose Us Slider routes
    Route::resource('why-choose-us-sliders', WhyChooseUsSliderController::class);
    Route::post('why-choose-us-sliders/reorder', [WhyChooseUsSliderController::class, 'reorder'])->name('why-choose-us-sliders.reorder');
    
    // Testimonial routes
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/reorder', [TestimonialController::class, 'reorder'])->name('testimonials.reorder');
    
    // Become A Partner routes
    Route::get('become-a-partner/edit', [BecomeAPartnerController::class, 'edit'])->name('become-a-partner.edit');
    Route::put('become-a-partner/update', [BecomeAPartnerController::class, 'update'])->name('become-a-partner.update');
    
}); 