
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
            @php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    $currentLocale = app()->getLocale();
    $supportedLocales = LaravelLocalization::getSupportedLocales();
@endphp

<ul class="header-right"> 
  {{-- LANGUAGE --}}
  <li class="language-nav custom-dropdown">
    <div class="language-wrap">
      <div class="language-content" id="current-language-display">
        
        {{-- FLAG --}}
        <span class="flag-icon">
          @if($currentLocale == 'az') 🇦🇿
          @elseif($currentLocale == 'en') 🇬🇧
          @elseif($currentLocale == 'ru') 🇷🇺
          @endif
        </span>

        {{-- NAME --}}
        <span class="ms-2">
          {{ $supportedLocales[$currentLocale]['name'] ?? strtoupper($currentLocale) }}
        </span>

        <i class="fa-solid fa-chevron-down"></i>
      </div>
    </div>

    {{-- DROPDOWN --}}
    <div class="custom-menu overflow-hidden" style="min-width: 150px;">
      <ul class="language-body">

        @foreach($supportedLocales as $localeCode => $properties)
          <li>
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
               class="d-flex align-items-center {{ $currentLocale === $localeCode ? 'active' : '' }}"
               style="{{ $currentLocale === $localeCode ? 'background-color: #f0f0f0; font-weight: bold;' : '' }}">

              {{-- FLAG --}}
              <span class="flag-icon">
                @if($localeCode == 'az') 🇦🇿
                @elseif($localeCode == 'en') 🇬🇧
                @elseif($localeCode == 'ru') 🇷🇺
                @endif
              </span>

              {{-- NAME --}}
              <span class="ms-2">{{ $properties['name'] }}</span>
            </a>
          </li>
        @endforeach

      </ul>
    </div>
  </li>

  {{-- PROFILE --}}
  <li class="profile-nav custom-dropdown">
    <div class="user-wrap">
      <div style="display: flex; align-items: center;" class="user-content">
        <h6>{{ Auth::user()->name ?? 'Admin' }}</h6>
        <p class="mb-0">
          <i class="fa-solid fa-chevron-down"></i>
        </p>
      </div>
    </div>

    <div class="custom-menu overflow-hidden">
      <ul class="profile-body">
        <li class="d-flex"> 
          <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit"
                    class="border-0 bg-transparent d-flex align-items-center w-100"
                    onclick="return confirm('{{ __('Çıxış etmək istədiyinizə əminsiniz?') }}')">

              <svg class="svg-color">
                <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#Login') }}"></use>
              </svg>

              <span class="ms-2">{{ __('Çıxış') }}</span>
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
                                        <p class="mb-0">Copyright {{ date('Y') }} © Admiro theme by pixelstrap. {{ __('Bütün hüquqlar qorunur.') }}</p>
              </div>
           
            </div>
          </div>
        </footer>
      </div>
    </div>
    @include('admin.layout.includes.foot')
    @yield('scripts')
    
    <style>
    /* Language Dropdown Custom Styling */
    .language-nav .custom-menu {
      border-radius: 12px !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
      border: 1px solid #e0e0e0 !important;
      margin-top: 10px !important;
      min-width: 160px !important;
      overflow: hidden !important;
    }
    
    .language-nav .custom-menu .language-body {
      padding: 8px 0 !important;
      margin: 0 !important;
    }
    
    .language-nav .custom-menu .language-body li {
      margin: 0 !important;
      padding: 0 !important;
    }
    
    .language-nav .custom-menu .language-body li a {
      padding: 10px 16px !important;
      margin: 0 !important;
      border-radius: 0 !important;
      transition: all 0.3s ease !important;
      display: flex !important;
      align-items: center !important;
      text-decoration: none !important;
      border-bottom: 1px solid #f5f5f5 !important;
    }
    
    .language-nav .custom-menu .language-body li:last-child a {
      border-bottom: none !important;
    }
    
    .language-nav .custom-menu .language-body li a:hover {
      background-color: #f8f9fa !important;
      color: #007bff !important;
    }
    
    .language-nav .custom-menu .language-body li a.active {
      background-color: #e3f2fd !important;
      color: #1976d2 !important;
      font-weight: 600 !important;
    }
    
    .language-nav .flag-icon {
      font-size: 18px !important;
      margin-right: 8px !important;
    }
    
    .language-nav .language-content {
      cursor: pointer !important;
      padding: 8px 12px !important;
      border-radius: 8px !important;
      transition: all 0.3s ease !important;
    }
    
    .language-nav .language-content:hover {
      background-color: #f8f9fa !important;
    }
    </style>
    
    <script>
    // Language switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const languageLinks = document.querySelectorAll('a[href^="?lang="]');
        
        languageLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const lang = this.href.split('=')[1];
                
                // Show loading state
                const currentDisplay = document.getElementById('current-language-display');
                if (currentDisplay) {
                    const originalContent = currentDisplay.innerHTML;
                    currentDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                }
                
                // Set cookie for language preference
                document.cookie = `locale=${lang}; path=/; max-age=31536000`; // 1 year
                
                // Reload page to apply language change
                setTimeout(() => {
                    window.location.reload();
                }, 300);
            });
        });
    });
    </script>
  </body>
</html>