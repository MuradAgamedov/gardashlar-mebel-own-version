<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Yalnız admin daxil ola bilər
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Bu panelə yalnız administratorlar daxil ola bilər.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Xoş gəldiniz, Administrator!');
        }

        return back()->withErrors([
            'email' => 'Daxil etdiyiniz məlumatlar səhvdir.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Sistemdən çıxış etdiniz.');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
