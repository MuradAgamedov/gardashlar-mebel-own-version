<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cache:clear-project-categories', function () {
    $languages = \App\Models\Language::all();
    $clearedCount = 0;

    foreach ($languages as $language) {
        $cacheKey = "api:project-categories:all:lang:{$language->id}";
        if (\Illuminate\Support\Facades\Cache::forget($cacheKey)) {
            $clearedCount++;
        }
    }

    $this->info("Cleared project categories cache for {$clearedCount} languages.");
})->purpose('Clear project categories cache for all languages');
