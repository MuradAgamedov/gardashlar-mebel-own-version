<?php

use App\Http\Controllers\Admin\UserController;
Route::get('registered-users', [UserController::class, 'index'])->name('registered-users');