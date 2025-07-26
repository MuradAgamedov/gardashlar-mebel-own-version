<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LanguageController;



// Language API Routes
Route::get('/languages', [LanguageController::class, 'index'])->name('api.languages.index');
