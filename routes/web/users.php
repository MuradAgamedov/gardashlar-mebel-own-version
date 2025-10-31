<?php

use App\Http\Controllers\Admin\UserController;
Route::post('registered-users', [UserController::class, 'index'])->name('registered-users');