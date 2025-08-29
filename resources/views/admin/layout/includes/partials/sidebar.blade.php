<aside class="page-sidebar">
  <div class="left-arrow" id="left-arrow"><i class="fas fa-chevron-left"></i></div>
  <div class="main-sidebar" id="main-sidebar">
    <ul class="sidebar-menu" id="simple-bar">

      <!-- CACHE CLEAR BUTTON -->
      <li class="sidebar-list">
        <button class="sidebar-link cache-clear-btn" id="cache-clear-btn" type="button">
          <i class="fas fa-broom"></i>
          <h6>Clear Cache</h6>
        </button>
      </li>

      <!-- GENERAL -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">General</h5></div></li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-language"></i>
          <h6>Languages & Dictionary</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.languages.index') }}">Languages</a></li>
          <li><a href="{{ route('admin.dictionaries.index') }}">Dictionary</a></li>
        </ul>
      </li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="{{ route('admin.pages.index') }}">
          <i class="fas fa-file-alt"></i>
          <h6>Pages</h6>
        </a>
      </li>

              <li class="sidebar-list">
          <a class="sidebar-link" href="{{ route('admin.contact-messages.index') }}">
            <i class="fas fa-envelope"></i>
            <h6>Contact Messages</h6>
            @php
              $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
              <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list">
          <a class="sidebar-link" href="{{ route('admin.become-a-partner-forms.index') }}">
            <i class="fas fa-handshake"></i>
            <h6>Partner Forms</h6>
            @php
              $unreadPartnerForms = \App\Models\BecomeAPartnerForm::where('is_read', false)->count();
            @endphp
            @if($unreadPartnerForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadPartnerForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list">
          <a class="sidebar-link" href="{{ route('admin.become-a-investor-forms.index') }}">
            <i class="fas fa-chart-line"></i>
            <h6>Investor Forms</h6>
            @php
              $unreadInvestorForms = \App\Models\BecomeAInvestorForm::where('is_read', false)->count();
            @endphp
            @if($unreadInvestorForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadInvestorForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list">
          <a class="sidebar-link" href="{{ route('admin.service-forms.index') }}">
            <i class="fas fa-cogs"></i>
            <h6>Service Forms</h6>
            @php
              $unreadServiceForms = \App\Models\ServiceForm::where('is_read', false)->count();
            @endphp
            @if($unreadServiceForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadServiceForms }}</span>
            @endif
          </a>
        </li>

        <li class="sidebar-list">
          <a class="sidebar-link" href="{{ route('admin.suggest-a-place-forms.index') }}">
            <i class="fas fa-map-marker-alt"></i>
            <h6>Place Forms</h6>
            @php
              $unreadPlaceForms = \App\Models\SuggestAPlaceForm::where('is_read', false)->count();
            @endphp
            @if($unreadPlaceForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadPlaceForms }}</span>
            @endif
          </a>
        </li>

      <!-- CONTENT MANAGEMENT -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">Content Management</h5></div></li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-layer-group"></i>
          <h6>Homepage Sections</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.why-choose-us.edit') }}">Why Choose Us</a></li>
          <li><a href="{{ route('admin.why-choose-us-sliders.index') }}">Why Choose Us Slider</a></li>
          <li><a href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
          <li><a href="{{ route('admin.home-page-projects.index') }}">Home Page Projects</a></li>
          <li><a href="{{ route('admin.home-page-categories.index') }}">Home Page Categories</a></li>
        </ul>
      </li>

      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-phone"></i>
          <h6>Contact</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.phones.index') }}">Phone</a></li>
          <li><a href="{{ route('admin.addresses.index') }}">Address</a></li>
          <li><a href="{{ route('admin.emails.index') }}">Email</a></li>
          <li><a href="{{ route('admin.maps.index') }}">Map</a></li>
          <li><a href="{{ route('admin.social-networks.index') }}">Social Networks</a></li>
        </ul>
      </li>

      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-photo-video"></i>
          <h6>KIV Management</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.kiv-categories.index') }}">KIV Categories</a></li>
          <li><a href="{{ route('admin.kivs.index') }}">KIVs</a></li>
        </ul>
      </li>

      <!-- PARTNERSHIP -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">Partnership</h5></div></li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-handshake"></i>
          <h6>Partner Sections</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.become-a-partner.edit') }}">Become A Partner</a></li>
          <li><a href="{{ route('admin.become-a-investor.edit') }}">Become An Investor</a></li>
          <li><a href="{{ route('admin.partner-sliders.index') }}">Partner Slider</a></li>
        </ul>
      </li>

      <!-- POLICIES -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">Policies & Info</h5></div></li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-shield-alt"></i>
          <h6>Customer Policies</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.customer-rights.edit') }}">Customer Rights</a></li>
          <li><a href="{{ route('admin.delivery-payment.edit') }}">Delivery & Payment</a></li>
          <li><a href="{{ route('admin.warranty-policy.edit') }}">Warranty Policy</a></li>
          <li><a href="{{ route('admin.refund-policy.edit') }}">Refund Policy</a></li>
          <li><a href="{{ route('admin.site-usage-rules.edit') }}">Site Usage Rules</a></li>
          <li><a href="{{ route('admin.privacy-policy.edit') }}">Privacy Policy</a></li>
        </ul>
      </li>

      <!-- SERVICES -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">Services</h5></div></li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-cogs"></i>
          <h6>Services</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.services.index') }}">Services</a></li>
          <li><a href="{{ route('admin.service-static-data.edit') }}">Service Static Data</a></li>
          <li><a href="{{ route('admin.individual-project-orders.index') }}">Individual Project Orders</a></li>
        </ul>
      </li>

      <li class="sidebar-list">
        <a class="sidebar-link" href="{{ route('admin.catalogs.index') }}">
          <i class="fas fa-book"></i>
          <h6>Catalogs</h6>
        </a>
      </li>

            <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-project-diagram"></i>
          <h6>Projects</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.project-categories.index') }}">Project Categories</a></li>
          <li><a href="{{ route('admin.projects.index') }}">Projects</a></li>
          <li><a href="{{ route('admin.home-page-project-categories.index') }}">Home Page Project Categories</a></li>
        </ul>
      </li>

      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-box"></i>
          <h6>Products</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.product-categories.index') }}">Product Categories</a></li>
          <li><a href="{{ route('admin.product-types.index') }}">Product Types</a></li>
          <li><a href="{{ route('admin.product-price-filters.index') }}">Price Filters</a></li>
          <li><a href="{{ route('admin.product-page-categories.index') }}">Product page categories</a></li>
          <li><a href="{{ route('admin.hero-categories.index') }}">Hero Categories</a></li>
          <li><a href="{{ route('admin.hero-products.index') }}">Hero Products</a></li>

          <li><a href="{{ route('admin.product-colors.index') }}">Product Colors</a></li>
          <li><a href="{{ route('admin.product-tags.index') }}">Product Tags</a></li>
          <li><a href="{{ route('admin.products.index') }}">Products</a></li>
          <li><a href="{{ route('admin.catalogs.index') }}">Catalogs</a></li>

        </ul>
      </li>


      <!-- OTHERS -->
      <li class="sidebar-main-title"><div><h5 class="sidebar-title">Other Sections</h5></div></li>
      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-th-list"></i>
          <h6>General Content</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.certificates.index') }}">Certificates</a></li>
          <li><a href="{{ route('admin.our-goals.index') }}">Our Goals</a></li>
          <li><a href="{{ route('admin.simple-images.index') }}">Simple Images</a></li>
          <li><a href="{{ route('admin.currencies.index') }}">Currencies</a></li>
          <li><a href="{{ route('admin.suggest-a-place.edit') }}">Suggest A Place</a></li>
          <li><a href="{{ route('admin.suggestions-complaints.edit') }}">Suggestions & Complaints</a></li>
          <li><a href="{{ route('admin.suggestions-complaints-forms.index') }}">Suggestions & Complaints Forms
            @php
              $unreadSuggestionsForms = \App\Models\SuggestionsComplaintsForm::where('is_read', false)->count();
            @endphp
            @if($unreadSuggestionsForms > 0)
              <span class="badge bg-danger ms-2">{{ $unreadSuggestionsForms }}</span>
            @endif
          </a></li>
          <li><a href="{{ route('admin.suggestions.index') }}">Suggestions</a></li>
          <li><a href="{{ route('admin.abouts.index') }}">About us</a></li>
          <li><a href="{{ route('admin.administrations.index') }}">Administrations</a></li>
          <li><a href="{{ route('admin.companies.index') }}">Companies</a></li>
          <li><a href="{{ route('admin.company-management.edit') }}">Company Management</a></li>
          <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
          <li><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
          <li><a href="{{ route('admin.vacancies.index') }}">Vacancies</a></li>
        </ul>
      </li>

      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-blog"></i>
          <h6>Blogs</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
                      <ul class="sidebar-submenu">
                <li><a href="{{ route('admin.blog-categories.index') }}">Blog Categories</a></li>
                <li><a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a></li>
              </ul>
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
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i><h6>Clearing...</h6>';
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
          this.innerHTML = '<i class="fas fa-check text-success"></i><h6>Cache Cleared!</h6>';
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
        this.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i><h6>Error!</h6>';
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
</style>
