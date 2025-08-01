<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\HomePageCategoryController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\ProductTypeProductController;
use App\Http\Controllers\Api\HeroCategoryController;
use App\Http\Controllers\Api\WhyChooseUsController;

// Language API Routes
Route::get('/languages', [LanguageController::class, 'index'])->name('api.languages.index');

// Page API Routes
Route::get('/pages', [PageController::class, 'index'])->name('api.pages.index');
Route::get('/pages/{keyword}', [PageController::class, 'findByKeyword'])->name('api.pages.find');

// HomePageCategory API Routes
Route::get('/homepage-categories', [HomePageCategoryController::class, 'index'])->name('api.homepage-categories.index');

// HeroCategory API Routes
Route::get('/hero-categories', [HeroCategoryController::class, 'index'])->name('api.hero-categories.index');
Route::get('/hero-categories/{categoryId}/products', [HeroCategoryController::class, 'getProductsByCategory'])->name('api.hero-categories.products');

// ProductType API Routes
Route::get('/product-types', [ProductTypeController::class, 'index'])->name('api.product-types.index');

// ProductType Products API Routes
Route::get('/product-types/{productTypeId}/products', [ProductTypeProductController::class, 'index'])->name('api.product-type-products.index');

// Why Choose Us API Routes
Route::get('/why-choose-us', [WhyChooseUsController::class, 'index'])->name('api.why-choose-us.index');
