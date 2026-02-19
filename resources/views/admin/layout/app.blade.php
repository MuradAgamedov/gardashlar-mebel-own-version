
<!DOCTYPE html >
<html lang="en">
  <head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

   @include('admin.layout.includes.head')
  </head>
  <body>
    <!-- page-wrapper Start-->
    @include('admin.layout.includes.partials.tap-top')
    @include('admin.layout.includes.partials.loader')


    <div class="page-wrapper compact-wrapper" id="pageWrapper"> 
      <header class="page-header row">
        <div class="logo-wrapper d-flex align-items-center col-auto"><a href="/"><img class="light-logo img-fluid" src="{{ asset('logo.svg') }}" alt="logo"/><img class="dark-logo img-fluid" src="{{ asset('logo.svg') }}" alt="logo"/></a><a class="close-btn toggle-sidebar" href="javascript:void(0)">
            <svg class="svg-color">
              <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Category') }}"></use>
            </svg></a></div>
        <div class="page-main-header col d-flex justify-content-end">
          <div class="nav-right d-flex justify-content-end">
            <ul class="header-right"> 
              <li class="profile-nav custom-dropdown">
                <div class="user-wrap">
                  <div class="user-content">
                    <h6>{{ Auth::user()->name ?? 'Admin' }}</h6>
                    <p class="mb-0">Admin<i class="fa-solid fa-chevron-down"></i></p>
                  </div>
                </div>
                <div class="custom-menu overflow-hidden">
                  <ul class="profile-body">
                    <li class="d-flex"> 
                      <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="border-0 bg-transparent d-flex align-items-center w-100" 
                                onclick="return confirm('Are you sure you want to logout?')">
                          <svg class="svg-color">
                            <use href="../assets/svg/iconly-sprite.svg#Login"></use>
                          </svg>
                          <span class="ms-2">Log Out</span>
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </header>
      <!-- Page Body Start-->
      <div class="page-body-wrapper"> 
      @include('admin.layout.includes.partials.sidebar')
        @yield("content")
        <footer class="footer"> 
          <div class="container-fluid">
            <div class="row"> 
              <div class="col-md-6 footer-copyright">
                                        <p class="mb-0">Copyright {{ date('Y') }} © Admiro theme by pixelstrap. All rights reserved.</p>
              </div>
           
            </div>
          </div>
        </footer>
      </div>
    </div>
    @include('admin.layout.includes.foot')
    @yield('scripts')
  </body>
</html>