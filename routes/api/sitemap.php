<?php

use App\Http\Controllers\Api\Sitemap\VacanciesSitemapController;
use App\Http\Controllers\Api\Sitemap\ServicesSitemapController;
use App\Http\Controllers\Api\Sitemap\CompaignsSitemapController;
use App\Http\Controllers\Api\Sitemap\BlogsSitemapController;
use App\Http\Controllers\Api\Sitemap\NewsSitemapController;
use App\Http\Controllers\Api\Sitemap\ProjectSitemapController;
use App\Http\Controllers\Api\Sitemap\ProductSitemapController;

use Illuminate\Support\Facades\Route;

Route::prefix('sitemap')->name('api.sitemap.')->group(function () {
    Route::get('/vacancies', [VacanciesSitemapController::class, 'index'])->name('vacancies');
    Route::get('/services', [ServicesSitemapController::class, 'index'])->name('services');
    Route::get('/compaigns', [CompaignsSitemapController::class, 'index'])->name('compaigns');
    Route::get('/blogs', [BlogsSitemapController::class, 'index'])->name('blogs');
    Route::get('/news', [NewsSitemapController::class, 'index'])->name('news');
    Route::get('/projects', [ProjectSitemapController::class, 'index'])->name('projects');
    Route::get('/products', [ProductSitemapController::class, 'index'])->name('products');
});
