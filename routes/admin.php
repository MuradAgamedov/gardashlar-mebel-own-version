<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
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
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\CompanyManagementController;

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
    
    // FAQ routes
    Route::resource('faqs', FaqController::class);
    Route::post('faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
    
    // Company Management routes
    Route::get('company-management/edit', [CompanyManagementController::class, 'edit'])->name('company-management.edit');
    Route::put('company-management/update', [CompanyManagementController::class, 'update'])->name('company-management.update');
    
    // Image Upload route for CKEditor
    Route::post('upload/image', [CompanyManagementController::class, 'uploadImage'])->name('upload.image');
    
});