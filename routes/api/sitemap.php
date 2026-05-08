<?php

use App\Http\Controllers\Api\Sitemap\VacanciesSitemapController;
use Illuminate\Support\Facades\Route;

Route::prefix('sitemap')->name('api.sitemap.')->group(function () {
    Route::get('/vacancies', [VacanciesSitemapController::class, 'index'])->name('vacancies');
});
