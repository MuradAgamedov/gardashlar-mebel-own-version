<aside class="page-sidebar">
  <div class="left-arrow" id="left-arrow"><i class="fas fa-chevron-left"></i></div>
  <div class="main-sidebar" id="main-sidebar">
    <ul class="sidebar-menu" id="simple-bar">

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
          <li><a href="{{ route('admin.catalogs.index') }}">Catalogs</a></li>
          <li><a href="{{ route('admin.individual-project-orders.index') }}">Individual Project Orders</a></li>
        </ul>
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
          <li><a href="{{ route('admin.images.index') }}">Images</a></li>
          <li><a href="{{ route('admin.simple-images.index') }}">Simple Images</a></li>
          <li><a href="{{ route('admin.suggest-a-place.edit') }}">Suggest A Place</a></li>
          <li><a href="{{ route('admin.suggestions-complaints.edit') }}">Suggestions & Complaints</a></li>
          <li><a href="{{ route('admin.suggestions.index') }}">Suggestions</a></li>
          <li><a href="{{ route('admin.abouts.index') }}">About us</a></li>
          <li><a href="{{ route('admin.companies.index') }}">Companies</a></li>
          <li><a href="{{ route('admin.company-management.edit') }}">Company Management</a></li>
          <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
          <li><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
          <li><a href="{{ route('admin.vacancies.index') }}">Vacancies</a></li>
        </ul>
      </li>

      <li class="sidebar-list">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fas fa-home"></i>
          <h6>Home Page Sections</h6>
          <i class="fas fa-chevron-down float-end"></i>
        </a>
        <ul class="sidebar-submenu">
          <li><a href="{{ route('admin.home-page-projects.index') }}">Home Page Projects</a></li>
          <li><a href="{{ route('admin.home-page-categories.index') }}">Home Page Categories</a></li>
        </ul>
      </li>



    </ul>
  </div>
  <div class="right-arrow" id="right-arrow"><i class="fas fa-chevron-right"></i></div>
</aside>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const dropdowns = document.querySelectorAll('.sidebar-link[href="javascript:void(0)"]');
  dropdowns.forEach(link => {
    link.addEventListener('click', () => {
      const submenu = link.nextElementSibling;
      submenu.classList.toggle('open');
    });
  });
});
</script>

<style>
.sidebar-submenu {
  display: none;
  padding-left: 20px;
}
.sidebar-submenu.open {
  display: block;
}
</style>
