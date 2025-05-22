<?php

namespace App\Providers;

use App\Services\CacheManager;
use App\Services\WhatsAppService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftarkan CacheManager sebagai singleton
        $this->app->singleton(CacheManager::class);

        // Register WhatsAppService
        $this->app->singleton(WhatsAppService::class, function ($app) {
            return new WhatsAppService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tambahkan SQL query logging untuk debug product update
        \DB::listen(function ($query) {
            if (strpos($query->sql, 'products') !== false && strpos($query->sql, 'update') !== false) {
                \Log::debug('SQL QUERY:', [
                    'query' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });
    }
}
