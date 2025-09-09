<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin panel login">
    <meta name="keywords" content="admin, login">
    <meta name="author" content="Admin">
    <title>Admin Login</title>
    
    <!-- Favicon icon-->
    <link rel="icon" href="{{ asset('admin/assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.png') }}" type="image/x-icon">
    
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,200;6..12,300;6..12,400;6..12,500;6..12,600;6..12,700;6..12,800;6..12,900;6..12,1000&display=swap" rel="stylesheet">
    
    <!-- Flag icon css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/vendors/flag-icon.css') }}">
    <!-- iconly-icon-->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/iconly-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bulk-style.css') }}">
    <!-- iconly-icon-->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/themify.css') }}">
    <!--fontawesome-->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/fontawesome-min.css') }}">
    <!-- Whether Icon css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/vendors/weather-icons/weather-icons.min.css') }}">
    <!-- App css -->
    <link id="color" rel="stylesheet" href="{{ asset('admin/assets/css/color-1.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
</head>
<body>
    <!-- tap on top starts-->
    <div class="tap-top"><i class="iconly-Arrow-Up icli"></i></div>
    <!-- tap on tap ends-->
    
    <!-- loader-->
    <div class="loader-wrapper">
        <div class="loader"><span></span><span></span><span></span><span></span><span></span></div>
    </div>
    
        <!-- login page start-->
    <div class="container-fluid p-0">
      <div class="row m-0">
        <div class="col-12 p-0">    
          <div class="login-card login-dark" style="background-image: url('{{ asset('GARDASHLAR.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    <div>
                        <div>
                            <a class="logo" href="{{ route('admin.login') }}">
                                <img class="img-fluid m-auto" src="{{ asset('logo.svg') }}" alt="logo" style="max-width: 200px; height: auto;">
                            </a>
                        </div>
                        <div class="login-main"> 
                            <form class="theme-form" method="POST" action="{{ route('admin.login.post') }}">
                                @csrf
                                <h2 class="text-center">Sign in to account</h2>
                                <p class="text-center">Enter your email & password to login</p>
                                
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                <div class="form-group">
                                    <label class="col-form-label">Email Address</label>
                                    <input class="form-control @error('email') is-invalid @enderror" 
                                           type="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           placeholder="admin@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-form-label">Password</label>
                                    <div class="form-input position-relative">
                                        <input class="form-control @error('password') is-invalid @enderror" 
                                               type="password" 
                                               name="password" 
                                               required 
                                               placeholder="*********">
                                        <div class="show-hide"><span class="show"></span></div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group mb-0 checkbox-checked">
                                    <div class="form-check checkbox-solid-info">
                                        <input class="form-check-input" 
                                               id="remember" 
                                               type="checkbox" 
                                               name="remember" 
                                               value="1">
                                        <label class="form-check-label" for="remember">Remember password</label>
                                    </div>
                                    
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jquery-->
    <script src="{{ asset('admin/assets/js/vendors/jquery/jquery.min.js') }}"></script>
    <!-- bootstrap js-->
    <script src="{{ asset('admin/assets/js/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('admin/assets/js/vendors/bootstrap/dist/js/popper.min.js') }}" defer></script>
    <!--fontawesome-->
    <script src="{{ asset('admin/assets/js/vendors/font-awesome/fontawesome-min.js') }}"></script>
    <!-- password_show-->
    <script src="{{ asset('admin/assets/js/password.js') }}"></script>
    <!-- custom script -->
    <script src="{{ asset('admin/assets/js/script.js') }}"></script>
</body>
</html> 