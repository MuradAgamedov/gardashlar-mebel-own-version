@php
// Helper function to check if current route belongs to a section
function isActiveSection($prefixes) {
    $currentRoute = Route::currentRouteName() ?? '';
    foreach ((array) $prefixes as $prefix) {
        if (str_starts_with($currentRoute, $prefix)) {
            return true;
        }
    }
    return false;
}

function isActiveRoute($route) {
    return Route::is($route) ? 'active' : '';
}
@endphp

<aside class="page-sidebar">
  <div class="left-arrow" id="left-arrow"><i class="fas fa-chevron-left"></i></div>
  <div class="main-sidebar" id="main-sidebar">
    <ul class="sidebar-menu" id="simple-bar">

      <!-- CACHE CLEAR BUTTON -->
      <li class="sidebar-list">
        <button class="sidebar-link cache-clear-btn" id="cache-clear-btn" type="button">
          <i class="fas fa-broom"></i>
          <h6>{{ __('Clear Cache') }}</h6>
        </button>
      </li>

      <!-- SIDEBAR SEARCH -->
      <li class="sidebar-list sidebar-search-wrapper" style="padding: 10px 15px;">
        <div class="sidebar-search-container" style="position: relative;">
          <input type="text" 
                 id="sidebar-search" 
                 class="form-control form-control-sm sidebar-search-input" 
                 placeholder="{{ __('Search menu...') }}"
                 style="border-radius: 20px; padding-left: 15px; padding-right: 15px; background: #f7fafc; color: #000; border: 1px solid #cbd5e0; font-size: 13px; height: 38px; width: 100%;">
          <small style="display: block; margin-top: 5px; color: #000; font-size: 11px; text-align: center;">məs: məhsul, sifariş, xəbər</small>
        </div>
      </li>

      <!-- GENERAL -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">{{ __('General') }}</h5></div></li>
      <li class="sidebar-list {{ isActiveSection(['admin.languages', 'admin.dictionaries']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-language"></i>
          <h6>{{ __('Languages & Dictionary') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.languages') ? 'active' : '' }}"><a href="{{ route('admin.languages.index') }}">{{ __('Languages') }}</a></li>
          <li class="{{ isActiveSection('admin.dictionaries') ? 'active' : '' }}"><a href="{{ route('admin.dictionaries.index') }}">{{ __('Dictionary') }}</a></li>
        </ul>
      </li>
      <li class="sidebar-list {{ isActiveSection('admin.pages') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.pages.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>{{ __('Pages') }}</h6>
        </a>
      </li>
      <li class="sidebar-list {{ isActiveSection('admin.all-seo-pages') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.all-seo-pages.edit') }}">
          <i class="fas fa-search-plus"></i>
          <h6>{{ __('All SEO Pages') }}</h6>
        </a>
      </li>
        <li class="sidebar-list {{ isActiveSection('admin.popup-ads') ? 'active' : '' }}">
    <a class="sidebar-link" href="{{ route('admin.popup-ads.index') }}">
        <i class="fas fa-rectangle-ad"></i>
        <h6>{{ __('Popup Ads') }}</h6>
    </a>
</li>

      <li class="sidebar-list {{ isActiveSection('admin.seo-pages') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.seo-pages.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>{{ __('SEO Pages') }}</h6>
        </a>
      </li>

      <li class="sidebar-list {{ isActiveSection('admin.roulette-actions') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.roulette-actions.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>{{ __('Roulette Actions') }}</h6>
        </a>
      </li>
      <li class="sidebar-list {{ isActiveSection('admin.orders') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.orders.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>{{ __('Orders') }}</h6>
        </a>
      </li>
      <li class="sidebar-list {{ isActiveSection('admin.credit-cards') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.credit-cards.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>{{ __('Credit Cards') }}</h6>
        </a>
      </li>
      <li class="sidebar-list {{ isActiveSection('admin.comments') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.comments.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>{{ __('Comments') }}</h6>
        </a>
      </li>



              <li class="sidebar-list {{ isActiveSection('admin.contact-messages') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.contact-messages.index') }}">
            <i class="fas fa-envelope"></i>
            <h6>{{ __('Contact Messages') }}</h6>
            @php
              $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
              <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list {{ isActiveSection('admin.become-a-partner-forms') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.become-a-partner-forms.index') }}">
            <i class="fas fa-handshake"></i>
            <h6>{{ __('Partner Forms') }}</h6>
            @php
              $unreadPartnerForms = \App\Models\BecomeAPartnerForm::where('is_read', false)->count();
            @endphp
            @if($unreadPartnerForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadPartnerForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list {{ isActiveSection('admin.become-a-investor-forms') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.become-a-investor-forms.index') }}">
            <i class="fas fa-chart-line"></i>
            <h6>{{ __('Investor Forms') }}</h6>
            @php
              $unreadInvestorForms = \App\Models\BecomeAInvestorForm::where('is_read', false)->count();
            @endphp
            @if($unreadInvestorForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadInvestorForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list {{ isActiveSection('admin.service-forms') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.service-forms.index') }}">
            <i class="fas fa-cogs"></i>
            <h6>{{ __('Service Forms') }}</h6>
            @php
              $unreadServiceForms = \App\Models\ServiceForm::where('is_read', false)->count();
            @endphp
            @if($unreadServiceForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadServiceForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list {{ isActiveSection('admin.suggest-a-place-forms') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.suggest-a-place-forms.index') }}">
            <i class="fas fa-map-marker-alt"></i>
            <h6>{{ __('Place Forms') }}</h6>
            @php
              $unreadPlaceForms = \App\Models\SuggestAPlaceForm::where('is_read', false)->count();
            @endphp
            @if($unreadPlaceForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadPlaceForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list {{ isActiveSection('admin.vacancy-forms') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.vacancy-forms.index') }}">
            <i class="fas fa-briefcase"></i>
            <h6>{{ __('Vacancy Forms') }}</h6>
            @php
              $unreadVacancyForms = \App\Models\VacancyForm::where('is_read', false)->count();
            @endphp
            @if($unreadVacancyForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadVacancyForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list {{ isActiveSection('admin.subscribers') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.subscribers.index') }}">
            <i class="fas fa-envelope-open-text"></i>
            <h6>{{ __('Subscribers') }}</h6>
            @php
              $subscriberCount = \App\Models\Subscriber::count();
            @endphp
            @if($subscriberCount > 0)
              <span class="badge bg-info ms-2">{{ $subscriberCount }}</span>
            @endif
          </a>
        </li>

      <!-- CONTENT MANAGEMENT -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">{{ __('Content Management') }}</h5></div></li>
      <li class="sidebar-list {{ isActiveSection(['admin.why-choose-us', 'admin.why-choose-us-sliders', 'admin.testimonials', 'admin.home-page-projects', 'admin.home-page-categories', 'admin.home-page-discount']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-layer-group"></i>
          <h6>{{ __('Homepage Sections') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.why-choose-us') ? 'active' : '' }}"><a href="{{ route('admin.why-choose-us.edit') }}">{{ __('Why Choose Us') }}</a></li>
          <li class="{{ isActiveSection('admin.why-choose-us-sliders') ? 'active' : '' }}"><a href="{{ route('admin.why-choose-us-sliders.index') }}">{{ __('Why Choose Us Slider') }}</a></li>
          <li class="{{ isActiveSection('admin.testimonials') ? 'active' : '' }}"><a href="{{ route('admin.testimonials.index') }}">{{ __('Testimonials') }}</a></li>
          <li class="{{ isActiveSection('admin.home-page-projects') ? 'active' : '' }}"><a href="{{ route('admin.home-page-projects.index') }}">{{ __('Home Page Projects') }}</a></li>
          <li class="{{ isActiveSection('admin.home-page-categories') ? 'active' : '' }}"><a href="{{ route('admin.home-page-categories.index') }}">{{ __('Home Page Categories') }}</a></li>
          <li class="{{ isActiveSection('admin.home-page-discount') ? 'active' : '' }}"><a href="{{route('admin.home-page-discount.edit')}}">{{ __('Home Page Discount') }}</a></li>
        </ul>
      </li>

      <li class="sidebar-list {{ isActiveSection(['admin.phones', 'admin.addresses', 'admin.emails', 'admin.maps', 'admin.social-networks']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-phone"></i>
          <h6>{{ __('Contact') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.phones') ? 'active' : '' }}"><a href="{{ route('admin.phones.index') }}">{{ __('Phone') }}</a></li>
          <li class="{{ isActiveSection('admin.addresses') ? 'active' : '' }}"><a href="{{ route('admin.addresses.index') }}">{{ __('Address') }}</a></li>
          <li class="{{ isActiveSection('admin.emails') ? 'active' : '' }}"><a href="{{ route('admin.emails.index') }}">{{ __('Email') }}</a></li>
          <li class="{{ isActiveSection('admin.maps') ? 'active' : '' }}"><a href="{{ route('admin.maps.index') }}">{{ __('Map') }}</a></li>
          <li class="{{ isActiveSection('admin.social-networks') ? 'active' : '' }}"><a href="{{ route('admin.social-networks.index') }}">{{ __('Social Networks') }}</a></li>
        </ul>
      </li>

      <li class="sidebar-list {{ isActiveSection(['admin.kiv-categories', 'admin.kivs']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-photo-video"></i>
          <h6>{{ __('KIV Management') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.kiv-categories') ? 'active' : '' }}"><a href="{{ route('admin.kiv-categories.index') }}">{{ __('KIV Categories') }}</a></li>
          <li class="{{ isActiveSection('admin.kivs') ? 'active' : '' }}"><a href="{{ route('admin.kivs.index') }}">{{ __('KIVs') }}</a></li>
        </ul>
      </li>

      <!-- PARTNERSHIP -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">{{ __('Partnership') }}</h5></div></li>
      <li class="sidebar-list {{ isActiveSection(['admin.become-a-partner', 'admin.become-a-investor', 'admin.partner-sliders']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-handshake"></i>
          <h6>{{ __('Partner Sections') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.become-a-partner') ? 'active' : '' }}"><a href="{{ route('admin.become-a-partner.edit') }}">{{ __('Become A Partner') }}</a></li>
          <li class="{{ isActiveSection('admin.become-a-investor') ? 'active' : '' }}"><a href="{{ route('admin.become-a-investor.edit') }}">{{ __('Become An Investor') }}</a></li>
          <li class="{{ isActiveSection('admin.partner-sliders') ? 'active' : '' }}"><a href="{{ route('admin.partner-sliders.index') }}">{{ __('Partner Slider') }}</a></li>
        </ul>
      </li>

      <!-- POLICIES -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">{{ __('Policies & Info') }}</h5></div></li>
      <li class="sidebar-list {{ isActiveSection(['admin.customer-rights', 'admin.delivery-payment', 'admin.warranty-policy', 'admin.refund-policy', 'admin.site-usage-rules', 'admin.privacy-policy']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-shield-alt"></i>
          <h6>{{ __('Customer Policies') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.customer-rights') ? 'active' : '' }}"><a href="{{ route('admin.customer-rights.edit') }}">{{ __('Customer Rights') }}</a></li>
          <li class="{{ isActiveSection('admin.delivery-payment') ? 'active' : '' }}"><a href="{{ route('admin.delivery-payment.edit') }}">{{ __('Delivery & Payment') }}</a></li>
          <li class="{{ isActiveSection('admin.warranty-policy') ? 'active' : '' }}"><a href="{{ route('admin.warranty-policy.edit') }}">{{ __('Warranty Policy') }}</a></li>
          <li class="{{ isActiveSection('admin.refund-policy') ? 'active' : '' }}"><a href="{{ route('admin.refund-policy.edit') }}">{{ __('Refund Policy') }}</a></li>
          <li class="{{ isActiveSection('admin.site-usage-rules') ? 'active' : '' }}"><a href="{{ route('admin.site-usage-rules.edit') }}">{{ __('Site Usage Rules') }}</a></li>
          <li class="{{ isActiveSection('admin.privacy-policy') ? 'active' : '' }}"><a href="{{ route('admin.privacy-policy.edit') }}">{{ __('Privacy Policy') }}</a></li>
        </ul>
      </li>

      <!-- SERVICES -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">{{ __('Services') }}</h5></div></li>
      <li class="sidebar-list {{ isActiveSection(['admin.services', 'admin.service-static-data', 'admin.individual-project-orders']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-cogs"></i>
          <h6>{{ __('Services') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.services') ? 'active' : '' }}"><a href="{{ route('admin.services.index') }}">{{ __('Services') }}</a></li>
          <li class="{{ isActiveSection('admin.service-static-data') ? 'active' : '' }}"><a href="{{ route('admin.service-static-data.edit') }}">{{ __('Service Static Data') }}</a></li>
          <li class="{{ isActiveSection('admin.individual-project-orders') ? 'active' : '' }}"><a href="{{ route('admin.individual-project-orders.index') }}">{{ __('Individual Project Orders') }}</a></li>
        </ul>
      </li>

    

            <li class="sidebar-list {{ isActiveSection(['admin.project-categories', 'admin.projects', 'admin.home-page-project-categories']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-project-diagram"></i>
          <h6>{{ __('Projects') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.project-categories') ? 'active' : '' }}"><a href="{{ route('admin.project-categories.index') }}">{{ __('Project Categories') }}</a></li>
          <li class="{{ isActiveSection('admin.projects') ? 'active' : '' }}"><a href="{{ route('admin.projects.index') }}">{{ __('Projects') }}</a></li>
          <li class="{{ isActiveSection('admin.home-page-project-categories') ? 'active' : '' }}"><a href="{{ route('admin.home-page-project-categories.index') }}">{{ __('Home Page Project Categories') }}</a></li>
        </ul>
      </li>

      <li class="sidebar-list {{ isActiveSection(['admin.products', 'admin.product-categories', 'admin.product-types', 'admin.product-price-filters', 'admin.product-page-categories', 'admin.hero-categories', 'admin.hero-products', 'admin.product-colors', 'admin.product-tags', 'admin.catalogs']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-box"></i>
          <h6>{{ __('Products') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.product-categories') ? 'active' : '' }}"><a href="{{ route('admin.product-categories.index') }}">{{ __('Product Categories') }}</a></li>
          <li class="{{ isActiveSection('admin.product-types') ? 'active' : '' }}"><a href="{{ route('admin.product-types.index') }}">{{ __('Product Types') }}</a></li>
          <li class="{{ isActiveSection('admin.product-price-filters') ? 'active' : '' }}"><a href="{{ route('admin.product-price-filters.index') }}">{{ __('Price Filters') }}</a></li>
          <li class="{{ isActiveSection('admin.product-page-categories') ? 'active' : '' }}"><a href="{{ route('admin.product-page-categories.index') }}">{{ __('Product page categories') }}</a></li>
          <li class="{{ isActiveSection('admin.hero-categories') ? 'active' : '' }}"><a href="{{ route('admin.hero-categories.index') }}">{{ __('Hero Categories') }}</a></li>
          <li class="{{ isActiveSection('admin.hero-products') ? 'active' : '' }}"><a href="{{ route('admin.hero-products.index') }}">{{ __('Hero Products') }}</a></li>
          <li class="{{ isActiveSection('admin.product-colors') ? 'active' : '' }}"><a href="{{ route('admin.product-colors.index') }}">{{ __('Product Colors') }}</a></li>
          <li class="{{ isActiveSection('admin.product-tags') ? 'active' : '' }}"><a href="{{ route('admin.product-tags.index') }}">{{ __('Product Tags') }}</a></li>
          <li class="{{ isActiveSection('admin.products') ? 'active' : '' }}"><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
          <li class="{{ isActiveSection('admin.catalogs') ? 'active' : '' }}"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catalogs') }}</a></li>
        </ul>
      </li>


      <!-- OTHERS -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">{{ __('Other Sections') }}</h5></div></li>
      <li class="sidebar-list {{ isActiveSection(['admin.certificates', 'admin.our-goals', 'admin.simple-images', 'admin.currencies', 'admin.suggest-a-place', 'admin.suggestions-complaints', 'admin.suggestions-complaints-forms', 'admin.suggestions', 'admin.abouts', 'admin.administrations', 'admin.campaigns', 'admin.actions', 'admin.company-management', 'admin.reports', 'admin.faqs', 'admin.vacancies']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-th-list"></i>
          <h6>{{ __('General Content') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li class="{{ isActiveSection('admin.certificates') ? 'active' : '' }}"><a href="{{ route('admin.certificates.index') }}">{{ __('Certificates') }}</a></li>
          <li class="{{ isActiveSection('admin.our-goals') ? 'active' : '' }}"><a href="{{ route('admin.our-goals.index') }}">{{ __('Our Goals') }}</a></li>
          <li class="{{ isActiveSection('admin.simple-images') ? 'active' : '' }}"><a href="{{ route('admin.simple-images.index') }}">{{ __('Simple Images') }}</a></li>
          <li class="{{ isActiveSection('admin.currencies') ? 'active' : '' }}"><a href="{{ route('admin.currencies.index') }}">{{ __('Currencies') }}</a></li>
          <li class="{{ isActiveSection('admin.suggest-a-place') ? 'active' : '' }}"><a href="{{ route('admin.suggest-a-place.edit') }}">{{ __('Suggest A Place') }}</a></li>
          <li class="{{ isActiveSection('admin.suggestions-complaints') ? 'active' : '' }}"><a href="{{ route('admin.suggestions-complaints.edit') }}">{{ __('Suggestions & Complaints') }}</a></li>
          <li class="{{ isActiveSection('admin.suggestions-complaints-forms') ? 'active' : '' }}"><a href="{{ route('admin.suggestions-complaints-forms.index') }}">{{ __('Suggestions & Complaints Forms') }}
            @php
              $unreadSuggestionsForms = \App\Models\SuggestionsComplaintsForm::where('is_read', false)->count();
            @endphp
            @if($unreadSuggestionsForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadSuggestionsForms }}</span>
            @endif
          </a></li>
          <li class="{{ isActiveSection('admin.suggestions') ? 'active' : '' }}"><a href="{{ route('admin.suggestions.index') }}">{{ __('Suggestions') }}</a></li>
          <li class="{{ isActiveSection('admin.abouts') ? 'active' : '' }}"><a href="{{ route('admin.abouts.index') }}">{{ __('About us') }}</a></li>
          <li class="{{ isActiveSection('admin.administrations') ? 'active' : '' }}"><a href="{{ route('admin.administrations.index') }}">{{ __('Administrations') }}</a></li>
          <li class="{{ isActiveSection('admin.campaigns') ? 'active' : '' }}"><a href="{{ route('admin.campaigns.index') }}">{{ __('Campaigns') }}</a></li>
          <li class="{{ isActiveSection('admin.actions') ? 'active' : '' }}"><a href="{{ route('admin.actions.index') }}">{{ __('Actions') }}</a></li>
          <li class="{{ isActiveSection('admin.company-management') ? 'active' : '' }}"><a href="{{ route('admin.company-management.edit') }}">{{ __('Company Management') }}</a></li>
          <li class="{{ isActiveSection('admin.reports') ? 'active' : '' }}"><a href="{{ route('admin.reports.index') }}">{{ __('Reports') }}</a></li>
          <li class="{{ isActiveSection('admin.faqs') ? 'active' : '' }}"><a href="{{ route('admin.faqs.index') }}">{{ __('FAQs') }}</a></li>
          <li class="{{ isActiveSection('admin.vacancies') ? 'active' : '' }}"><a href="{{ route('admin.vacancies.index') }}">{{ __('Vacancies') }}</a></li>
        </ul>
      </li>

      <li class="sidebar-list {{ isActiveSection(['admin.blog-categories', 'admin.blog-posts']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-blog"></i>
          <h6>{{ __('Blogs') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
                      <ul class="sidebar-submenu">
                <li class="{{ isActiveSection('admin.blog-categories') ? 'active' : '' }}"><a href="{{ route('admin.blog-categories.index') }}">{{ __('Blog Categories') }}</a></li>
                <li class="{{ isActiveSection('admin.blog-posts') ? 'active' : '' }}"><a href="{{ route('admin.blog-posts.index') }}">{{ __('Blog Posts') }}</a></li>
              </ul>
      </li>

      <li class="sidebar-list {{ isActiveSection(['admin.news-categories', 'admin.news-posts']) ? 'active' : '' }}">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-newspaper"></i>
          <h6>{{ __('News') }}</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
                      <ul class="sidebar-submenu">
                <li class="{{ isActiveSection('admin.news-categories') ? 'active' : '' }}"><a href="{{ route('admin.news-categories.index') }}">{{ __('News Categories') }}</a></li>
                <li class="{{ isActiveSection('admin.news-posts') ? 'active' : '' }}"><a href="{{ route('admin.news-posts.index') }}">{{ __('News Posts') }}</a></li>
              </ul>
      </li>

      <li class="sidebar-list {{ isActiveSection('admin.registered-users') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{route('admin.registered-users')}}">
          <i class="fas fa-blog"></i>
          <h6>{{ __('Users') }}</h6>
        </a>
                
      </li>

    </ul>
  </div>
  <div class="right-arrow" id="right-arrow"><i class="fas fa-chevron-right"></i></div>
</aside>
<script>
document.addEventListener('DOMContentLoaded', () => {
  console.log('Sidebar script loaded');
  
  const dropdowns = document.querySelectorAll('.sidebar-link[href="javascript:void(0)"]');
  console.log('Found dropdowns:', dropdowns.length);
  
  dropdowns.forEach((link, index) => {
    link.addEventListener('click', () => {
      console.log('Dropdown clicked:', index);
      const submenu = link.nextElementSibling;
      if (submenu && submenu.classList.contains('sidebar-submenu')) {
        submenu.classList.toggle('open');
        console.log('Submenu toggled:', submenu.classList.contains('open'));
      }
    });
  });
  
  // Auto-open Services section if we're on catalogs page
  const currentPath = window.location.pathname;
  if (currentPath.includes('/catalogs')) {
    const servicesLink = document.querySelector('a[href="javascript:void(0)"] h6');
    if (servicesLink && servicesLink.textContent.trim() === 'Services') {
      const servicesDropdown = servicesLink.closest('.sidebar-list');
      const submenu = servicesDropdown.querySelector('.sidebar-submenu');
      if (submenu) {
        submenu.classList.add('open');
        console.log('Services submenu auto-opened');
      }
    }
  }

  // Cache clear button functionality
  const cacheClearBtn = document.getElementById('cache-clear-btn');
  if (cacheClearBtn) {
    cacheClearBtn.addEventListener('click', async function() {
      // Disable button and show loading state
      const originalText = this.innerHTML;
      this.innerHTML = '<i class="fas fa-sync-alt"></i><h6>' + '{{ __("Clearing...") }}' + '</h6>';
      this.disabled = true;
      
      try {
        const response = await fetch('{{ route("admin.clear-cache") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Show success message
          this.innerHTML = '<i class="fas fa-check text-success"></i><h6>' + '{{ __("Cache Cleared!") }}' + '</h6>';
          setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
          }, 2000);
        } else {
          throw new Error(result.message);
        }
      } catch (error) {
        console.error('Error clearing cache:', error);
        // Show error message
        this.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i><h6>' + '{{ __("Error!") }}' + '</h6>';
        setTimeout(() => {
          this.innerHTML = originalText;
          this.disabled = false;
        }, 2000);
      }
    });
  }
});
</script>

<style>
.sidebar-submenu {
  display: none;
  padding-left: 20px;
  background-color: rgba(0,0,0,0.05);
  border-radius: 5px;
  margin: 5px 0;
}
.sidebar-submenu.open {
  display: block !important;
}
.sidebar-submenu li {
  padding: 8px 15px;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}
.sidebar-submenu li:last-child {
  border-bottom: none;
}
.sidebar-submenu a {
  color: #666;
  text-decoration: none;
  font-size: 14px;
}
.sidebar-submenu a:hover {
  color: #007bff;
}

/* Cache Clear Button Styles */
.cache-clear-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  margin: 10px 15px;
  padding: 12px 15px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.cache-clear-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.cache-clear-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.cache-clear-btn .text-success {
  color: #28a745 !important;
}

.cache-clear-btn .text-danger {
  color: #dc3545 !important;
}

.cache-clear-btn h6 {
  margin: 0;
  font-weight: 600;
  font-size: 14px;
}

/* Sidebar Search Styles */
.sidebar-search-container {
  position: relative;
}

.sidebar-search-wrapper .form-control::placeholder {
  color: #4a5568;
  font-size: 13px;
  opacity: 1;
}

.sidebar-search-wrapper .form-control:focus {
  background: #fff !important;
  border-color: #667eea !important;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  outline: none;
}

/* Search Highlight Styles */
.sidebar-list.search-match > .sidebar-link {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border-radius: 8px;
  animation: pulse 1.5s infinite;
}

.sidebar-list.search-match > .sidebar-link i,
.sidebar-list.search-match > .sidebar-link h6 {
  color: white !important;
}

.sidebar-submenu li.search-match {
  background: rgba(102, 126, 234, 0.1);
  border-left: 3px solid #667eea;
}

.sidebar-submenu li.search-match a {
  color: #667eea;
  font-weight: 600;
}

@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
  50% { box-shadow: 0 0 0 8px rgba(102, 126, 234, 0); }
}

.sidebar-list.hidden-by-search,
.sidebar-submenu li.hidden-by-search {
  display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('sidebar-search');
    const sidebarMenu = document.getElementById('simple-bar');
    
    if (!searchInput || !sidebarMenu) return;
    
    // Get all menu items with their text
    const menuItems = [];
    const allListItems = sidebarMenu.querySelectorAll('.sidebar-list');
    
    allListItems.forEach(item => {
        const link = item.querySelector('.sidebar-link');
        const h6 = item.querySelector('h6');
        const submenu = item.querySelector('.sidebar-submenu');
        
        if (h6) {
            const text = h6.textContent.toLowerCase().trim();
            menuItems.push({
                element: item,
                text: text,
                isParent: !!submenu,
                submenu: submenu
            });
        }
    });
    
    // Get all submenu items
    const submenuItems = [];
    sidebarMenu.querySelectorAll('.sidebar-submenu li').forEach(item => {
        const link = item.querySelector('a');
        if (link) {
            submenuItems.push({
                element: item,
                text: link.textContent.toLowerCase().trim()
            });
        }
    });
    
    // Search function
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        if (query === '') {
            // Reset all
            menuItems.forEach(item => {
                item.element.classList.remove('search-match', 'hidden-by-search');
                if (item.submenu) {
                    item.submenu.classList.remove('open');
                }
            });
            submenuItems.forEach(item => {
                item.element.classList.remove('search-match', 'hidden-by-search');
            });
            return;
        }
        
        // First pass: find and show matching submenu items
        let hasSubmenuMatch = false;
        submenuItems.forEach(item => {
            if (item.text.includes(query)) {
                item.element.classList.add('search-match');
                item.element.classList.remove('hidden-by-search');
                hasSubmenuMatch = true;
                
                // Show parent dropdown
                const parentLi = item.element.closest('.sidebar-list');
                if (parentLi) {
                    parentLi.classList.remove('hidden-by-search');
                    const parentSubmenu = parentLi.querySelector('.sidebar-submenu');
                    if (parentSubmenu) {
                        parentSubmenu.classList.add('open');
                    }
                }
            } else {
                item.element.classList.remove('search-match');
                item.element.classList.add('hidden-by-search');
            }
        });
        
        // Second pass: find and show matching parent items
        menuItems.forEach(item => {
            if (item.text.includes(query)) {
                item.element.classList.add('search-match');
                item.element.classList.remove('hidden-by-search');
                // Open the dropdown
                if (item.submenu) {
                    item.submenu.classList.add('open');
                }
            } else if (!hasSubmenuMatch || !item.submenu) {
                // Hide non-matching parent items unless they have matching children
                const hasVisibleChildren = item.submenu && 
                    item.submenu.querySelectorAll('li.search-match').length > 0;
                
                if (!hasVisibleChildren) {
                    item.element.classList.add('hidden-by-search');
                } else {
                    item.element.classList.remove('hidden-by-search');
                }
            }
        });
    });
    
    // Clear search on escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
        }
    });
});
</script>
