<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\DictionaryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\WhyChooseUsController;
use App\Http\Controllers\Admin\WhyChooseUsSliderController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\BecomeAPartnerController;
use App\Http\Controllers\Admin\BecomeAPartnerAdvantageController;
use App\Http\Controllers\Admin\BecomeAInvestorController;
use App\Http\Controllers\Admin\BecomeAInvestorAdvantageController;
use App\Http\Controllers\Admin\PartnerSliderController;
use App\Http\Controllers\Admin\SuggestAPlaceController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\KivCategoryController;
use App\Http\Controllers\Admin\KivController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SuggestionsComplaintsController;
use App\Http\Controllers\Admin\VacancyController;
use App\Http\Controllers\Admin\VacancyRequirementController;
use App\Http\Controllers\Admin\CustomerRightsController;
use App\Http\Controllers\Admin\DeliveryPaymentController;
use App\Http\Controllers\Admin\WarrantyPolicyController;
use App\Http\Controllers\Admin\RefundPolicyController;
use App\Http\Controllers\Admin\HomePageDiscountController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\CompanyManagementController;
use App\Http\Controllers\Admin\SiteUsageRulesController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Admin\IndividualProjectOrderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceAdvantageController;
use App\Http\Controllers\Admin\ServiceStaticDataController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\PhoneTypeController;
use App\Http\Controllers\Admin\PhoneController;
use App\Http\Controllers\Admin\AddressTypeController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\HeroCategoryController;
use App\Http\Controllers\Admin\HeroProductController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectVideoController;
use App\Http\Controllers\Admin\ProjectImageController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\ProductTypeProductController;
use App\Http\Controllers\Admin\ProductPriceFilterController;
use App\Http\Controllers\Admin\HomePageProjectController;
use App\Http\Controllers\Admin\HomePageCategoryController;
use App\Http\Controllers\Admin\HomePageProjectCategoryController;
use App\Http\Controllers\Admin\ProductPageCategoryController;
use App\Http\Controllers\Admin\ProductColorController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductAccordionController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\OurGoalController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\SimpleImageController;
use App\Http\Controllers\Admin\SocialNetworkController;
use App\Http\Controllers\Admin\AuthController;

Route::prefix('')->name('admin.')->group(function () {
    // Auth routes (no middleware)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Protected routes
    Route::middleware('admin.auth')->group(function () {
        // Dashboard routes
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Language routes
        Route::resource('languages', LanguageController::class);
        Route::post('languages/reorder', [LanguageController::class, 'reorder'])->name('languages.reorder');
        
        // Dictionary routes
        Route::resource('dictionaries', DictionaryController::class);
        
        // Page routes
        Route::resource('pages', PageController::class);
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
        
        // Become A Partner Advantages routes
        Route::resource('become-a-partner-advantages', BecomeAPartnerAdvantageController::class);
        Route::post('become-a-partner-advantages/reorder', [BecomeAPartnerAdvantageController::class, 'reorder'])->name('become-a-partner-advantages.reorder');
        
        // Become A Investor routes
        Route::get('become-a-investor/edit', [BecomeAInvestorController::class, 'edit'])->name('become-a-investor.edit');
        Route::put('become-a-investor/update', [BecomeAInvestorController::class, 'update'])->name('become-a-investor.update');
        
        // Become A Investor Advantages routes
        Route::resource('become-a-investor-advantages', BecomeAInvestorAdvantageController::class);
        Route::post('become-a-investor-advantages/reorder', [BecomeAInvestorAdvantageController::class, 'reorder'])->name('become-a-investor-advantages.reorder');
        
        // Partner Slider routes
        Route::resource('partner-sliders', PartnerSliderController::class);
        Route::post('partner-sliders/reorder', [PartnerSliderController::class, 'reorder'])->name('partner-sliders.reorder');
        
        // Suggest A Place routes
        Route::get('suggest-a-place/edit', [SuggestAPlaceController::class, 'edit'])->name('suggest-a-place.edit');
        Route::put('suggest-a-place/update', [SuggestAPlaceController::class, 'update'])->name('suggest-a-place.update');
        
        // Certificate routes
        Route::resource('certificates', CertificateController::class);
        Route::post('certificates/reorder', [CertificateController::class, 'reorder'])->name('certificates.reorder');
        
        // KIV Category routes
        Route::resource('kiv-categories', KivCategoryController::class);
        Route::post('kiv-categories/reorder', [KivCategoryController::class, 'reorder'])->name('kiv-categories.reorder');
        
        // KIV routes
        Route::resource('kivs', KivController::class);
        Route::post('kivs/reorder', [KivController::class, 'reorder'])->name('kivs.reorder');
        
        // Report routes
        Route::resource('reports', ReportController::class);
        Route::post('reports/reorder', [ReportController::class, 'reorder'])->name('reports.reorder');
        
        // Suggestions and Complaints routes
        Route::get('suggestions-complaints/edit', [SuggestionsComplaintsController::class, 'edit'])->name('suggestions-complaints.edit');
        Route::put('suggestions-complaints/update', [SuggestionsComplaintsController::class, 'update'])->name('suggestions-complaints.update');
        
        // Vacancy routes
        Route::resource('vacancies', VacancyController::class);
        Route::post('vacancies/reorder', [VacancyController::class, 'reorder'])->name('vacancies.reorder');
        
        // Vacancy Requirement routes
        Route::resource('vacancy-requirements', VacancyRequirementController::class);
        Route::get('vacancy-requirements/create/{vacancy_id}', [VacancyRequirementController::class, 'createForVacancy'])->name('vacancy-requirements.create-for-vacancy');
        Route::post('vacancy-requirements/reorder', [VacancyRequirementController::class, 'reorder'])->name('vacancy-requirements.reorder');
        
        // Customer Rights routes
        Route::get('customer-rights/edit', [CustomerRightsController::class, 'edit'])->name('customer-rights.edit');
        Route::put('customer-rights/update', [CustomerRightsController::class, 'update'])->name('customer-rights.update');
        
        // Delivery & Payment routes
        Route::get('delivery-payment/edit', [DeliveryPaymentController::class, 'edit'])->name('delivery-payment.edit');
        Route::put('delivery-payment/update', [DeliveryPaymentController::class, 'update'])->name('delivery-payment.update');
        
        // Warranty Policy routes
        Route::get('warranty-policy/edit', [WarrantyPolicyController::class, 'edit'])->name('warranty-policy.edit');
        Route::put('warranty-policy/update', [WarrantyPolicyController::class, 'update'])->name('warranty-policy.update');
        
        // Refund Policy routes
        Route::get('refund-policy/edit', [RefundPolicyController::class, 'edit'])->name('refund-policy.edit');
        Route::put('refund-policy/update', [RefundPolicyController::class, 'update'])->name('refund-policy.update');
        
        // Home Page Discount routes
        Route::get('home-page-discount/edit', [HomePageDiscountController::class, 'edit'])->name('home-page-discount.edit');
        Route::put('home-page-discount/update', [HomePageDiscountController::class, 'update'])->name('home-page-discount.update');
        
        // FAQ routes
        Route::resource('faqs', FaqController::class);
        Route::post('faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
        
        // Company Management routes
        Route::get('company-management/edit', [CompanyManagementController::class, 'edit'])->name('company-management.edit');
        Route::put('company-management/update', [CompanyManagementController::class, 'update'])->name('company-management.update');
        
        // Site Usage Rules routes
        Route::get('site-usage-rules/edit', [SiteUsageRulesController::class, 'edit'])->name('site-usage-rules.edit');
        Route::put('site-usage-rules/update', [SiteUsageRulesController::class, 'update'])->name('site-usage-rules.update');
        
        // Privacy Policy routes
        Route::get('privacy-policy/edit', [PrivacyPolicyController::class, 'edit'])->name('privacy-policy.edit');
        Route::put('privacy-policy/update', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');
        
        // About routes
        Route::resource('abouts', AboutController::class);
        Route::post('abouts/reorder', [AboutController::class, 'reorder'])->name('abouts.reorder');

        // Company routes
        Route::resource('companies', CompanyController::class);
        Route::post('companies/reorder', [CompanyController::class, 'reorder'])->name('companies.reorder');
        
        // Suggestion routes
        Route::resource('suggestions', SuggestionController::class);
        Route::post('suggestions/reorder', [SuggestionController::class, 'reorder'])->name('suggestions.reorder');
        
        // Individual Project Order routes
        Route::resource('individual-project-orders', IndividualProjectOrderController::class);
        Route::post('individual-project-orders/reorder', [IndividualProjectOrderController::class, 'reorder'])->name('individual-project-orders.reorder');
        
        // Service routes
        Route::resource('services', ServiceController::class);
        Route::post('services/reorder', [ServiceController::class, 'updateOrder'])->name('services.reorder');
        Route::post('services/{id}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        
        // Service Advantages routes
        Route::get('services/{serviceId}/advantages', [ServiceAdvantageController::class, 'index'])->name('service-advantages.index');
        Route::get('services/{serviceId}/advantages/create', [ServiceAdvantageController::class, 'create'])->name('service-advantages.create');
        Route::post('services/{serviceId}/advantages', [ServiceAdvantageController::class, 'store'])->name('service-advantages.store');
        Route::get('services/{serviceId}/advantages/{advantageId}/edit', [ServiceAdvantageController::class, 'edit'])->name('service-advantages.edit');
        Route::put('services/{serviceId}/advantages/{advantageId}', [ServiceAdvantageController::class, 'update'])->name('service-advantages.update');
        Route::delete('services/{serviceId}/advantages/{advantageId}', [ServiceAdvantageController::class, 'destroy'])->name('service-advantages.destroy');
        Route::post('services/{serviceId}/advantages/reorder', [ServiceAdvantageController::class, 'updateOrder'])->name('service-advantages.reorder');
        Route::post('services/{serviceId}/advantages/{advantageId}/toggle-status', [ServiceAdvantageController::class, 'toggleStatus'])->name('service-advantages.toggle-status');
        
        // Service Static Data routes
        Route::get('service-static-data/edit', [ServiceStaticDataController::class, 'edit'])->name('service-static-data.edit');
        Route::put('service-static-data/update', [ServiceStaticDataController::class, 'update'])->name('service-static-data.update');
        
        // Catalog routes
        Route::resource('catalogs', CatalogController::class);
        Route::post('catalogs/reorder', [CatalogController::class, 'reorder'])->name('catalogs.reorder');
        
        // Phone Type routes
        Route::resource('phone-types', PhoneTypeController::class);
        Route::post('phone-types/reorder', [PhoneTypeController::class, 'reorder'])->name('phone-types.reorder');
        
        // Phone routes
        Route::resource('phones', PhoneController::class);
        Route::post('phones/reorder', [PhoneController::class, 'reorder'])->name('phones.reorder');
        Route::get('phone-types/{type}/phones/create', [PhoneController::class, 'createForType'])->name('phone-types.phones.create');
        
        // Address Type routes
        Route::resource('address-types', AddressTypeController::class);
        Route::post('address-types/reorder', [AddressTypeController::class, 'reorder'])->name('address-types.reorder');
        
        // Address routes
        Route::resource('addresses', AddressController::class);
        Route::post('addresses/reorder', [AddressController::class, 'reorder'])->name('addresses.reorder');
        Route::get('address-types/{addressType}/addresses/create', [AddressController::class, 'createForType'])->name('address-types.addresses.create');
        
        // Email routes
        Route::resource('emails', EmailController::class);
        Route::post('emails/reorder', [EmailController::class, 'reorder'])->name('emails.reorder');
        
        // Map routes
        Route::resource('maps', MapController::class);
        Route::post('maps/reorder', [MapController::class, 'reorder'])->name('maps.reorder');
        
        // Project Categories routes
        Route::resource('project-categories', ProjectCategoryController::class)->except(['show']);
        Route::post('project-categories/reorder', [ProjectCategoryController::class, 'reorder'])->name('project-categories.reorder');
        
        // Hero Categories routes
        Route::resource('hero-categories', HeroCategoryController::class);
        Route::post('hero-categories/reorder', [HeroCategoryController::class, 'reorder'])->name('hero-categories.reorder');
        
        // Hero Products routes
        Route::resource('hero-products', HeroProductController::class);
        Route::post('hero-products/reorder', [HeroProductController::class, 'reorder'])->name('hero-products.reorder');
        Route::post('hero-products/search-products', [HeroProductController::class, 'searchProducts'])->name('hero-products.search-products');
        
        // Projects routes
        Route::resource('projects', ProjectController::class);
        Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
        
        // Project Videos routes
        Route::resource('projects.videos', ProjectVideoController::class);
        Route::post('projects/{project}/videos/reorder', [ProjectVideoController::class, 'reorder'])->name('projects.videos.reorder');
        
        // Project Images routes
        Route::resource('projects.images', ProjectImageController::class);
        Route::post('projects/{project}/images/reorder', [ProjectImageController::class, 'reorder'])->name('projects.images.reorder');
        Route::post('projects/{project}/images/{image}/set-main', [ProjectImageController::class, 'setMainImage'])->name('projects.images.set-main');
        
        // Product Categories routes
        Route::resource('product-categories', ProductCategoryController::class);
        Route::post('product-categories/reorder', [ProductCategoryController::class, 'reorder'])->name('product-categories.reorder');
        Route::post('product-categories/delete-selected', [ProductCategoryController::class, 'deleteSelected'])->name('product-categories.delete-selected');
        
        // Product Types routes
        Route::resource('product-types', ProductTypeController::class);

// Product Type Products routes
Route::get('product-types/{productType}/products', [ProductTypeProductController::class, 'index'])->name('product-type-products.index');
Route::post('product-types/{productType}/products/{product}/toggle-homepage', [ProductTypeProductController::class, 'toggleHomePage'])->name('product-type-products.toggle-homepage');
Route::get('product-types/{productType}/products/search', [ProductTypeProductController::class, 'search'])->name('product-type-products.search');
        Route::post('product-types/reorder', [ProductTypeController::class, 'reorder'])->name('product-types.reorder');
        
        // Product Price Filter routes
        Route::resource('product-price-filters', ProductPriceFilterController::class);
        Route::post('product-price-filters/reorder', [ProductPriceFilterController::class, 'reorder'])->name('product-price-filters.reorder');
        
        // Home Page Projects routes
        Route::get('home-page-projects/get-by-category', [HomePageProjectController::class, 'getProjectsByCategory'])->name('home-page-projects.get-by-category');
        Route::resource('home-page-projects', HomePageProjectController::class);
        Route::post('home-page-projects/reorder', [HomePageProjectController::class, 'reorder'])->name('home-page-projects.reorder');
        
        // Home Page Categories routes
        Route::resource('home-page-categories', HomePageCategoryController::class);
        Route::post('home-page-categories/reorder', [HomePageCategoryController::class, 'reorder'])->name('home-page-categories.reorder');
        
        // Home Page Project Categories routes
        Route::resource('home-page-project-categories', HomePageProjectCategoryController::class)->except(['show']);
        Route::post('home-page-project-categories/reorder', [HomePageProjectCategoryController::class, 'reorder'])->name('home-page-project-categories.reorder');
        
        // Product Page Categories routes
        Route::resource('product-page-categories', ProductPageCategoryController::class);
        Route::post('product-page-categories/reorder', [ProductPageCategoryController::class, 'reorder'])->name('product-page-categories.reorder');
        
        // Product Colors routes
        Route::delete('product-colors/bulk-delete', [ProductColorController::class, 'bulkDelete'])->name('product-colors.bulk-delete');
        Route::resource('product-colors', ProductColorController::class);
        
        // Product Tags routes
        Route::resource('product-tags', ProductTagController::class);
        Route::post('product-tags/reorder', [ProductTagController::class, 'reorder'])->name('product-tags.reorder');
        Route::post('product-tags/generate-slug', [ProductTagController::class, 'generateSlug'])->name('product-tags.generate-slug');

        // Products
        Route::delete('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::post('products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');
        Route::post('products/generate-slug', [ProductController::class, 'generateSlug'])->name('products.generate-slug');
        Route::resource('products', ProductController::class);

        // Product Accordion routes
        Route::resource('products.accordions', ProductAccordionController::class);
        Route::post('products/{product}/accordions/reorder', [ProductAccordionController::class, 'reorder'])->name('products.accordions.reorder');

        // Product Images routes
        Route::resource('products.images', ProductImageController::class);
        Route::post('products/{product}/images/reorder', [ProductImageController::class, 'reorder'])->name('products.images.reorder');
        
        // Our Goals routes
        Route::resource('our-goals', OurGoalController::class);
        Route::post('our-goals/reorder', [OurGoalController::class, 'reorder'])->name('our-goals.reorder');
        
        // Images routes
        Route::resource('images', ImageController::class);
        Route::post('products/{product}/images/{image}/set-main', [ProductImageController::class, 'setMainImage'])->name('products.images.set-main');
        
        // Simple Images routes
        Route::resource('simple-images', SimpleImageController::class);

        // Social Networks routes
        Route::resource('social-networks', SocialNetworkController::class);
        Route::post('social-networks/reorder', [SocialNetworkController::class, 'reorder'])->name('social-networks.reorder');
        Route::post('social-networks/{socialNetwork}/toggle-status', [SocialNetworkController::class, 'toggleStatus'])->name('social-networks.toggle-status');

        // Image Upload route for CKEditor
        Route::post('upload/image', [CompanyManagementController::class, 'uploadImage'])->name('upload.image');
    });
});