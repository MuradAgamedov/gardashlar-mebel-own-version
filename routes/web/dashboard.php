<?php
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::post('clear-cache', [DashboardController::class, 'clearCache'])->name('clear-cache');
