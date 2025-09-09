<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Language;

class ClearProjectCategoriesCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-project-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear project categories cache for all languages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $languages = Language::all();
        $clearedCount = 0;

        foreach ($languages as $language) {
            $cacheKey = "api:project-categories:all:lang:{$language->id}";
            if (Cache::forget($cacheKey)) {
                $clearedCount++;
            }
        }

        $this->info("Cleared project categories cache for {$clearedCount} languages.");
        
        return 0;
    }
}
