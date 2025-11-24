<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Project;
use App\Models\Order;
use App\Models\ContactMessage;
use App\Models\BecomeAPartnerForm;
use App\Models\BecomeAInvestorForm;
use App\Models\BlogPost;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\SuggestionsComplaintsForm;
use App\Models\VacancyForm;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current date info
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->format('F Y');
        $currentDay = $currentDate->format('l j, M Y');

        // Statistics
        $stats = [
            'total_products' => Product::count(),
            'total_projects' => Project::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_blog_posts' => BlogPost::count(),
            'total_services' => Service::count(),
            'total_testimonials' => Testimonial::count(),
            'total_subscribers' => Subscriber::count(),
        ];

        // Unread notifications
        $notifications = [
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'unread_partner_forms' => BecomeAPartnerForm::where('is_read', false)->count(),
            'unread_investor_forms' => BecomeAInvestorForm::where('is_read', false)->count(),
            'unread_suggestions' => SuggestionsComplaintsForm::where('is_read', false)->count(),
            'unread_vacancy_forms' => VacancyForm::where('is_read', false)->count(),
        ];

        // Recent activities
        $recentOrders = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $recentMessages = ContactMessage::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentBlogPosts = BlogPost::with(['translates'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Monthly statistics
        $monthlyStats = [
            'orders_this_month' => Order::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count(),
            'messages_this_month' => ContactMessage::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count(),
            'users_this_month' => User::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count(),
        ];

        // Chart data for last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('M j'),
                'orders' => Order::whereDate('created_at', $date)->count(),
                'messages' => ContactMessage::whereDate('created_at', $date)->count(),
                'users' => User::whereDate('created_at', $date)->count(),
            ];
        }

        // Top products by orders - using custom method since orders use JSON
        $topProducts = Product::select('products.*')
            ->selectSub(function ($query) {
                $query->from('orders')
                    ->whereRaw("FIND_IN_SET(products.id, REPLACE(REPLACE(product_ids, '[', ''), ']', ''))")
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->selectRaw('COUNT(*)');
            }, 'orders_count')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();


        return view('admin.dashboard.index', compact(
            'stats',
            'notifications',
            'recentOrders',
            'recentMessages',
            'recentBlogPosts',
            'monthlyStats',
            'chartData',
            'topProducts',
            'currentMonth',
            'currentDay'
        ));
    }

    public function clearCache()
    {
        try {
            // Clear backend caches
            Artisan::call('optimize:clear');
            Artisan::call('cache:clear');

            // Get frontend cache URL from .env
            $frontendCacheUrl = env('CLEAR_FRONTEND_CACHE');

            if ($frontendCacheUrl) {
                // Send request to frontend cache clear endpoint
                $response = @file_get_contents($frontendCacheUrl);

                // Optional: you can log response or show it in JSON
                $frontendResponse = $response ?: 'No response received';
            } else {
                $frontendResponse = 'CLEAR_FRONTEND_CACHE not set in .env';
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!',
                'frontend_response' => $frontendResponse
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }
}
