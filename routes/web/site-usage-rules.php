<?php
use App\Http\Controllers\Admin\SiteUsageRulesController;

Route::get('site-usage-rules/edit', [SiteUsageRulesController::class, 'edit'])->name('site-usage-rules.edit');
Route::put('site-usage-rules/update', [SiteUsageRulesController::class, 'update'])->name('site-usage-rules.update');