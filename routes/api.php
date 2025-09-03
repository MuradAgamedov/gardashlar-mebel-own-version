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
use App\Http\Controllers\Api\ProjectCategoryController;
use App\Http\Controllers\Api\ProjectController;
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
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ServiceStaticDataController;
use App\Http\Controllers\Api\IndividualProjectOrderController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\BecomeAPartnerController;
use App\Http\Controllers\Api\BecomeAInvestorController;
use App\Http\Controllers\Api\SuggestAPlaceController;
use App\Http\Controllers\Api\KivController;
use App\Http\Controllers\Api\BecomeAPartnerFormController;
use App\Http\Controllers\Api\BecomeAInvestorFormController;
use App\Http\Controllers\Api\ServiceFormController;
use App\Http\Controllers\Api\SuggestAPlaceFormController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SuggestionsComplaintsController;
use App\Http\Controllers\Api\SuggestionsComplaintsFormController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\PrivacyPolicyController;
use App\Http\Controllers\Api\CustomerRightsController;
use App\Http\Controllers\Api\DeliveryPaymentController;
use App\Http\Controllers\Api\WarrantyPolicyController;
use App\Http\Controllers\Api\RefundPolicyController;
use App\Http\Controllers\Api\CompanyManagementController;
use App\Http\Controllers\Api\SiteUsageRulesController;
use App\Http\Controllers\Api\SuggestionController;
use App\Http\Controllers\Api\AuthController;


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

// Project Categories API Routes
Route::get('/project-categories', [ProjectCategoryController::class, 'index'])->name('api.project-categories.index');

// Projects API Routes
Route::get('/projects/{slug}', [ProjectController::class, 'getByCategorySlug'])->name('api.projects.by-category');
Route::get('/project/{slug}', [ProjectController::class, 'getBySlug'])->name('api.project.details');

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
Route::get('/phones', [PhoneController::class, 'phones'])->name('api.phones');

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

// Services API Routes
Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('api.services.show');

// Service Static Data API Routes
Route::get('/service-static-data', [ServiceStaticDataController::class, 'index'])->name('api.service-static-data.index');

// Individual Project Orders API Routes
Route::get('/individual-project-orders', [IndividualProjectOrderController::class, 'index'])->name('api.individual-project-orders.index');

// FAQs API Routes
Route::get('/faqs', [FaqController::class, 'index'])->name('api.faqs.index');

// Catalogs API Routes
Route::get('/catalogs_years', [CatalogController::class, 'index'])->name('api.catalogs.index');
Route::get('/catalogs/{id}', [CatalogController::class, 'show'])->name('api.catalogs.show');

// Maps API Routes
Route::get('/maps', [MapController::class, 'index'])->name('api.maps.index');

// Contact Messages API Routes (Frontend only)
Route::post('/contact-messages', [ContactMessageController::class, 'store'])->name('api.contact-messages.store');

// Become A Partner API Routes
Route::get('/become-a-partner', [BecomeAPartnerController::class, 'index'])->name('api.become-a-partner.index');

// Become A Investor API Routes
Route::get('/become-a-investor', [BecomeAInvestorController::class, 'index'])->name('api.become-a-investor.index');

// Suggest A Place API Routes
Route::get('/suggest-a-place', [SuggestAPlaceController::class, 'index'])->name('api.suggest-a-place.index');

// KIVs API Routes
Route::get('/kivs', [KivController::class, 'index'])->name('api.kivs.index');
Route::get('/image/download', [KivController::class, 'download'])->name('api.kivs.download');

// Become A Partner Form API Routes
Route::post('/become-a-partner-form', [BecomeAPartnerFormController::class, 'store'])->name('api.become-a-partner-form.store');

// Become A Investor Forms API Routes
Route::post('/become-a-investor-forms', [BecomeAInvestorFormController::class, 'store'])->name('api.become-a-investor-forms.store');

// Service Forms API Routes
Route::post('/service-forms', [ServiceFormController::class, 'store'])->name('api.service-forms.store');

// Suggest A Place Form API Routes
Route::post('/suggest-a-place-form', [SuggestAPlaceFormController::class, 'store'])->name('api.suggest-a-place-form.store');

// Reports API Routes
Route::get('/reports', [ReportController::class, 'index'])->name('api.reports.index');

// Suggestions & Complaints API Routes
Route::get('/suggestions-complaints', [SuggestionsComplaintsController::class, 'index'])->name('api.suggestions-complaints.index');

// Suggestions & Complaints Form API Routes
Route::post('/suggestions-complaints-form', [SuggestionsComplaintsFormController::class, 'store'])->name('api.suggestions-complaints-form.store');

// Vacancies API Routes
Route::get('/vacancies', [VacancyController::class, 'index'])->name('api.vacancies.index');
Route::get('/vacancies/{slug}', [VacancyController::class, 'show'])->name('api.vacancies.show');

// Privacy Policy API Routes
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('api.privacy-policy.index');

// Customer Rights API Routes
Route::get('/customer-rights', [CustomerRightsController::class, 'index'])->name('api.customer-rights.index');

// Delivery Payment API Routes
Route::get('/delivery-payment', [DeliveryPaymentController::class, 'index'])->name('api.delivery-payment.index');

// Warranty Policy API Routes
Route::get('/warranty-policy', [WarrantyPolicyController::class, 'index'])->name('api.warranty-policy.index');

// Refund Policy API Routes
Route::get('/refund-policy', [RefundPolicyController::class, 'index'])->name('api.refund-policy.index');

// Company Management API Routes
Route::get('/company-management', [CompanyManagementController::class, 'index'])->name('api.company-management.index');

// Site Usage Rules API Routes
Route::get('/site-usage-rules', [SiteUsageRulesController::class, 'index'])->name('api.site-usage-rules.index');

// Suggestions API Routes
Route::get('/suggestions', [SuggestionController::class, 'index'])->name('api.suggestions.index');
Route::get('/suggestions/{id}', [SuggestionController::class, 'show'])->name('api.suggestions.show');

// Auth API Routes
Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/send-otp', [AuthController::class, 'sendOTP'])->name('api.auth.send-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('api.auth.verify-otp');
Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
});
