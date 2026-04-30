@extends("admin.layout.app")

@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12"> 
                    <h2>{{ __('Admin Dashboard') }}</h2>
                    <p class="mb-0 text-title-gray">{{ __('Welcome back! Here\'s what\'s happening today.') }}</p>
                </div>
                <div class="col-sm-6 col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="iconly-Home icli svg-color"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">{{ __('Dashboard') }}</li>
                        <li class="breadcrumb-item active">{{ __('Overview') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Container-fluid starts-->
    <div class="container-fluid default-dashboard">
        <div class="row">
            <!-- Welcome Card -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="card welcome-banner">
                    <div class="card-header p-0 card-no-border">
                        <div class="welcome-card">
                            <img class="w-100 img-fluid" src="{{ asset('admin/assets/images/dashboard_image.avif') }}" alt=""/>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-center">
                            <h1>{{ __('Hello, Admin') }} <img src="{{ asset('admin/assets/images/dashboard-1/hand.png') }}" alt=""/></h1>
                        </div>
                        <p>{{ __('Welcome back! Here\'s your system overview for today.') }}</p>
                        <div class="d-flex align-center justify-content-end">
                            <span>
                                <svg class="stroke-icon">
                                    <use href="{{ asset('admin/assets/svg/icon-sprite.svg#watch') }}"></use>
                                </svg> 
                                {{ now()->format('g:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="col-xl-8 col-sm-6 mb-4">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="flex-shrink-0 bg-light-primary p-3 rounded">
                                        <i class="fas fa-box text-primary"></i>
                                    </div>
                                </div>
                                <h3 class="mb-1">{{ $stats['total_products'] }}</h3>
                                <p class="mb-0 text-muted">{{ __('Total Products') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="flex-shrink-0 bg-light-success p-3 rounded">
                                        <i class="fas fa-project-diagram text-success"></i>
                                    </div>
                                </div>
                                <h3 class="mb-1">{{ $stats['total_projects'] }}</h3>
                                <p class="mb-0 text-muted">{{ __('Projects') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="flex-shrink-0 bg-light-warning p-3 rounded">
                                        <i class="fas fa-shopping-cart text-warning"></i>
                                    </div>
                                </div>
                                <h3 class="mb-1">{{ $stats['total_orders'] }}</h3>
                                <p class="mb-0 text-muted">{{ __('Total Orders') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="flex-shrink-0 bg-light-info p-3 rounded">
                                        <i class="fas fa-users text-info"></i>
                                    </div>
                                </div>
                                <h3 class="mb-1">{{ $stats['total_users'] }}</h3>
                                <p class="mb-0 text-muted">{{ __('Total Users') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Recent Orders') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order ID') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->name ?? __('Guest User') }}</td>
                                        <td>${{ number_format($order->amount ?? 0, 2) }}</td>
                                        <td>{{ $order->created_at->translatedFormat('M d, Y') }}</td>
                                        <td>
                                            @if($order->status == 'completed')
                                                <span class="badge bg-success">{{ __('Completed') }}</span>
                                            @elseif($order->status == 'pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __(ucfirst($order->status)) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('No recent orders found') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ __('Recent Messages') }}</h4>
                        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            @forelse($recentMessages as $message)
                            <li class="d-flex align-items-center gap-3 mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar {{ $message->is_read ? 'bg-success' : 'bg-warning' }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-{{ $message->is_read ? 'check' : 'envelope' }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($message->subject, 25) }}</h6>
                                    <p class="mb-0 text-muted small">{{ $message->name }}</p>
                                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                            </li>
                            @empty
                            <li class="text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>{{ __('No recent messages') }}</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-blog fa-2x text-primary mb-3"></i>
                        <h4>{{ $stats['total_blog_posts'] }}</h4>
                        <p class="text-muted">{{ __('Blog Posts') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-cogs fa-2x text-success mb-3"></i>
                        <h4>{{ $stats['total_services'] }}</h4>
                        <p class="text-muted">{{ __('Services') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-star fa-2x text-warning mb-3"></i>
                        <h4>{{ $stats['total_testimonials'] }}</h4>
                        <p class="text-muted">{{ __('Testimonials') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-envelope fa-2x text-info mb-3"></i>
                        <h4>{{ $stats['total_subscribers'] }}</h4>
                        <p class="text-muted">{{ __('Subscribers') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Notifications Summary') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 col-sm-4 mb-3">
                                <a href="{{ route('admin.contact-messages.index') }}" class="text-decoration-none">
                                    <div class="text-center">
                                        <div class="badge bg-danger rounded-pill mb-2" style="font-size: 1.2em;">
                                            {{ $notifications['unread_messages'] }}
                                        </div>
                                        <p class="mb-0 small text-dark">{{ __('Unread Messages') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-3">
                                <a href="{{ route('admin.become-a-partner-forms.index') }}" class="text-decoration-none">
                                    <div class="text-center">
                                        <div class="badge bg-warning rounded-pill mb-2" style="font-size: 1.2em;">
                                            {{ $notifications['unread_partner_forms'] }}
                                        </div>
                                        <p class="mb-0 small text-dark">{{ __('Partner Forms') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-3">
                                <a href="{{ route('admin.become-a-investor-forms.index') }}" class="text-decoration-none">
                                    <div class="text-center">
                                        <div class="badge bg-info rounded-pill mb-2" style="font-size: 1.2em;">
                                            {{ $notifications['unread_investor_forms'] }}
                                        </div>
                                        <p class="mb-0 small text-dark">{{ __('Investor Forms') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-3">
                                <a href="{{ route('admin.suggestions-complaints-forms.index') }}" class="text-decoration-none">
                                    <div class="text-center">
                                        <div class="badge bg-success rounded-pill mb-2" style="font-size: 1.2em;">
                                            {{ $notifications['unread_suggestions'] }}
                                        </div>
                                        <p class="mb-0 small text-dark">{{ __('Suggestions') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-3">
                                <a href="{{ route('admin.vacancy-forms.index') }}" class="text-decoration-none">
                                    <div class="text-center">
                                        <div class="badge bg-primary rounded-pill mb-2" style="font-size: 1.2em;">
                                            {{ $notifications['unread_vacancy_forms'] }}
                                        </div>
                                        <p class="mb-0 small text-dark">{{ __('Vacancy Forms') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-3">
                                <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                                    <div class="text-center">
                                        <div class="badge bg-secondary rounded-pill mb-2" style="font-size: 1.2em;">
                                            {{ $monthlyStats['orders_this_month'] }}
                                        </div>
                                        <p class="mb-0 small text-dark">{{ __('Orders This Month') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
