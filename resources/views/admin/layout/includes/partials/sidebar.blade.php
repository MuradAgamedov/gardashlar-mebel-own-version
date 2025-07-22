 <!-- Page sidebar start-->
 <aside class="page-sidebar"> 
          <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
          <div class="main-sidebar" id="main-sidebar">
            <ul class="sidebar-menu" id="simple-bar">
              <li class="sidebar-main-title">
                <div>
                  <h5 class="lan-1 f-w-700 sidebar-title">General</h5>
                </div>
              </li>
              <li class="sidebar-list">
                <a class="sidebar-link" href="javascript:void(0)"> 
                  <svg class="stroke-icon">
                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Message') }}"></use>
                  </svg>
                  <h6>Languages and Dictionary</h6>
                  <i class="iconly-Arrow-Right-2 icli"></i>
                </a>
                <ul class="sidebar-submenu">
                  <li><a href="{{ route('admin.languages.index') }}">Languages</a></li>
                  <li><a href="#">Dictionary</a></li>
                </ul>
              </li>
              <li class="sidebar-list">
                <a class="sidebar-link" href="{{ route('admin.pages.index') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#File') }}"></use>
                  </svg>
                  <h6>Pages</h6>
                </a>
              </li>
              <li class="sidebar-list">
                <a class="sidebar-link" href="{{ route('admin.why-choose-us.edit') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Star') }}"></use>
                  </svg>
                  <h6>Why Choose Us</h6>
                </a>
              </li>
              <li class="sidebar-list">
                <a class="sidebar-link" href="{{ route('admin.why-choose-us-sliders.index') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Image') }}"></use>
                  </svg>
                  <h6>Why Choose Us Slider</h6>
                </a>
              </li>
              <li class="sidebar-list">
                <a class="sidebar-link" href="{{ route('admin.testimonials.index') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#User') }}"></use>
                  </svg>
                  <h6>Testimonials</h6>
                </a>
              </li>
                          <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.become-a-partner.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Handshake') }}"></use>
                </svg>
                <h6>Become A Partner</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.become-a-investor.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#TrendingUp') }}"></use>
                </svg>
                <h6>Become A Investor</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.partner-sliders.index') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Image') }}"></use>
                </svg>
                <h6>Partner Slider</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.suggest-a-place.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#MapPin') }}"></use>
                </svg>
                <h6>Suggest A Place</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.certificates.index') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Award') }}"></use>
                </svg>
                <h6>Certificates</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="javascript:void(0)">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Video') }}"></use>
                </svg>
                <h6>KIV Management</h6>
                <i class="iconly-Arrow-Right-2 icli"></i>
              </a>
              <ul class="sidebar-submenu">
                <li><a href="{{ route('admin.kiv-categories.index') }}">KIV Categories</a></li>
                <li><a href="{{ route('admin.kivs.index') }}">KIVs</a></li>
              </ul>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.reports.index') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#FileText') }}"></use>
                </svg>
                <h6>Reports</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.suggestions-complaints.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Message') }}"></use>
                </svg>
                <h6>Suggestions & Complaints</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.customer-rights.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Shield') }}"></use>
                </svg>
                <h6>Customer Rights</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.delivery-payment.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Truck') }}"></use>
                </svg>
                <h6>Delivery & Payment</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.warranty-policy.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Shield') }}"></use>
                </svg>
                <h6>Warranty Policy</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.refund-policy.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#RefreshCw') }}"></use>
                </svg>
                <h6>Refund Policy</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.faqs.index') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#HelpCircle') }}"></use>
                </svg>
                <h6>FAQs</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.company-management.edit') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Users') }}"></use>
                </svg>
                <h6>Company Management</h6>
              </a>
            </li>
            <li class="sidebar-list">
              <a class="sidebar-link" href="{{ route('admin.vacancies.index') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Briefcase') }}"></use>
                </svg>
                <h6>Vacancies</h6>
              </a>
            </li>
            </ul>
          </div>
          <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </aside>
        <!-- Page sidebar end-->