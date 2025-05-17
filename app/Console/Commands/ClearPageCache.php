<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all page cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Karena kita menggunakan prefix page_cache, kita bisa hapus berdasarkan pattern
        $cacheStore = Cache::getStore();
        
        // Jika store adalah instance yang mendukung flushPrefix
        if (method_exists($cacheStore, 'flushPrefix')) {
            $cacheStore->flushPrefix('page_cache');
            $this->info('Page cache cleared successfully using flushPrefix.');
            return;
        }
        
        // Fallback untuk store yang tidak mendukung flushPrefix
        Cache::flush();
        $this->warn('All cache cleared. Note: This cleared all cache, not just page cache.');
        
        return;
    }
} 