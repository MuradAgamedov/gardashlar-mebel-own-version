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
use App\Http\Controllers\Api\HomePageDiscountController;
use App\Http\Controllers\Api\HomePageProjectCategoryController;
use App\Http\Controllers\Api\SimpleImageController;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\Api\SocialNetworkController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\PartnerSliderController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\PhoneController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\AdministrationController;
use App\Http\Controllers\Api\OurGoalController;
use App\Http\Controllers\Api\CertificateController;

// Language API Routes
Route::get('/languages', [LanguageController::class, 'index'])->name('api.languages.index');

// Dictionary API Routes
Route::get('/dictionaries', [DictionaryController::class, 'index'])->name('api.dictionaries.index');
Route::get('/dictionaries/{keyword}', [DictionaryController::class, 'show'])->name('api.dictionaries.show');
Route::post('/dictionaries/by-keywords', [DictionaryController::class, 'getByKeywordsArray'])->name('api.dictionaries.by-keywords');

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

// Home Page Discount API Routes
Route::get('/home-page-discount', [HomePageDiscountController::class, 'index'])->name('api.home-page-discount.index');

// Home Page Project Categories API Routes
Route::get('/home-page-project-categories', [HomePageProjectCategoryController::class, 'index'])->name('api.home-page-project-categories.index');

// Simple Images API Routes
Route::get('/simple-images', [SimpleImageController::class, 'index'])->name('api.simple-images.index');
Route::get('/simple-images/{keyword}', [SimpleImageController::class, 'show'])->name('api.simple-images.show');
Route::post('/simple-images/by-keywords', [SimpleImageController::class, 'getByKeywordsArray'])->name('api.simple-images.by-keywords');

// Social Networks API Routes
Route::get('/social-networks', [SocialNetworkController::class, 'index'])->name('api.social-networks.index');
Route::get('/social-networks/available-types', [SocialNetworkController::class, 'getAvailableTypes'])->name('api.social-networks.available-types');
Route::get('/social-networks/{type}', [SocialNetworkController::class, 'show'])->name('api.social-networks.show');
Route::post('/social-networks/by-types', [SocialNetworkController::class, 'getByTypesArray'])->name('api.social-networks.by-types');

// Currencies API Routes
Route::get('/currencies', [CurrencyController::class, 'index'])->name('api.currencies.index');
Route::get('/currencies/available-codes', [CurrencyController::class, 'getAvailableCodes'])->name('api.currencies.available-codes');
Route::get('/currencies/{code}', [CurrencyController::class, 'show'])->name('api.currencies.show');
Route::post('/currencies/by-codes', [CurrencyController::class, 'getByCodesArray'])->name('api.currencies.by-codes');
Route::post('/currencies/convert', [CurrencyController::class, 'convert'])->name('api.currencies.convert');

// Partner Sliders API Routes
Route::get('/partners', [PartnerSliderController::class, 'index'])->name('api.partner-sliders.index');
Route::get('/partners/{id}', [PartnerSliderController::class, 'show'])->name('api.partner-sliders.show');
Route::get('/partners/with-all-translations', [PartnerSliderController::class, 'withAllTranslations'])->name('api.partner-sliders.with-all-translations');

// Testimonials API Routes
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('api.testimonials.index');
Route::get('/testimonials/{id}', [TestimonialController::class, 'show'])->name('api.testimonials.show');
Route::get('/testimonials-with-translations', [TestimonialController::class, 'withTranslations'])->name('api.testimonials.with-translations');

// Blog Posts API Routes
Route::get('/blog-posts', [BlogPostController::class, 'index'])->name('api.blog-posts.index');
Route::get('/blog-posts/{slug}', [BlogPostController::class, 'show'])->name('api.blog-posts.show');

// Blog Categories API Routes
Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('api.blog-categories.index');

// Phones API Routes
Route::get('/general-phones', [PhoneController::class, 'generalPhones'])->name('api.general-phones');

// Emails API Routes
Route::get('/emails', [EmailController::class, 'index'])->name('api.emails.index');

// Addresses API Routes
Route::get('/addresses', [AddressController::class, 'index'])->name('api.addresses.index');

// Abouts API Routes
Route::get('/abouts', [AboutController::class, 'index'])->name('api.abouts.index');

// Administrations API Routes
Route::get('/administrations', [AdministrationController::class, 'index'])->name('api.administrations.index');
Route::get('/our-goals', [OurGoalController::class, 'index'])->name('api.our-goals.index');
Route::get('/certificates', [CertificateController::class, 'index'])->name('api.certificates.index');
