<?php

use App\Http\Controllers\Api\Sitemap\VacanciesSitemapController;
use App\Http\Controllers\Api\Sitemap\ServicesSitemapController;
use Illuminate\Support\Facades\Route;

Route::prefix('sitemap')->name('api.sitemap.')->group(function () {
    Route::get('/vacancies', [VacanciesSitemapController::class, 'index'])->name('vacancies');
    Route::get('/services', [ServicesSitemapController::class, 'index'])->name('services');

});
