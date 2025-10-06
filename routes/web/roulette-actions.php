<?php
use App\Http\Controllers\Admin\RoluteeteActionController;

Route::resource('roulette-actions', RoluteeteActionController::class);
Route::patch('roulette-actions/{id}/toggle-status', [RoluteeteActionController::class, 'toggleStatus'])
->name('roulette-actions.toggleStatus');