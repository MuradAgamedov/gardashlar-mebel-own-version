<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        
        return view('admin.dashboard.index');
    }

    public function clearCache()
    {
        try {
            // Clear optimize cache
            Artisan::call('optimize:clear');
            
            // Clear application cache
            Artisan::call('cache:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }
} 