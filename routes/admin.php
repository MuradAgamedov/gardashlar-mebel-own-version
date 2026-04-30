<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;







use App\Http\Controllers\Admin\AuthController;



Route::group(
    [
        'prefix' => LaravelLocalization::setLocale()
    ], function() {
    
    Route::prefix('')->name('admin.')->group(function () {
    // Auth routes (no middleware)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Protected routes
    Route::middleware('admin.auth')->group(function () {

        require __DIR__.'/web/dashboard.php';
        require __DIR__.'/web/languages.php';
        require __DIR__.'/web/dictionaries.php';
        require __DIR__.'/web/orders.php';
        require __DIR__.'/web/credit-cards.php';
        require __DIR__.'/web/roulette-actions.php';
        require __DIR__.'/web/pages.php';
        require __DIR__.'/web/why-choose-us.php';
        require __DIR__.'/web/testimonials.php';
        require __DIR__.'/web/become-a-partner.php';
        require __DIR__.'/web/become-a-investor.php';
        require __DIR__.'/web/partners.php';
        require __DIR__.'/web/suggest-a-place.php';
        require __DIR__.'/web/certificates.php';
        require __DIR__.'/web/kivs.php';
        require __DIR__.'/web/reports.php';
        require __DIR__.'/web/suggestions-complaints.php';
        require __DIR__.'/web/vacancies.php';
        require __DIR__.'/web/customer-rights.php';
        require __DIR__.'/web/delivery-payment.php';
        require __DIR__.'/web/warranty-policy.php';
        require __DIR__.'/web/refund-policy.php';
        require __DIR__.'/web/home-page-discount.php';
        require __DIR__.'/web/faqs.php';
        require __DIR__.'/web/company-management.php';
        require __DIR__.'/web/site-usage-rules.php';
        require __DIR__.'/web/privacy-policy.php';
        require __DIR__.'/web/abouts.php';
        require __DIR__.'/web/administrations.php';
        require __DIR__.'/web/companies.php';
        require __DIR__.'/web/suggestions.php';
        require __DIR__.'/web/individual-project-orders.php';
        require __DIR__.'/web/services.php';
        require __DIR__.'/web/catalogs.php';
        require __DIR__.'/web/phones.php';
        require __DIR__.'/web/addresses.php';
        require __DIR__.'/web/emails.php';
        require __DIR__.'/web/maps.php';
        require __DIR__.'/web/hero.php';
        require __DIR__.'/web/projects.php';
        require __DIR__.'/web/products.php';
        require __DIR__.'/web/our-goals.php';
        require __DIR__.'/web/simple-images.php';
        require __DIR__.'/web/contact-messages.php';
        require __DIR__.'/web/subscribers.php';
        require __DIR__.'/web/become-a-partner-forms.php';
        require __DIR__.'/web/become-a-investor-forms.php';
        require __DIR__.'/web/blogs.php';
        require __DIR__.'/web/news.php';
        require __DIR__.'/web/social-networks.php';
        require __DIR__.'/web/currencies.php';
        require __DIR__.'/web/actions.php';
        require __DIR__.'/web/all-seo-pages.php';
        require __DIR__.'/web/seo-pages.php';
        require __DIR__.'/web/users.php';
        require __DIR__.'/web/users.php';
        require __DIR__.'/web/popupAd.php';

    });
       
    });
});