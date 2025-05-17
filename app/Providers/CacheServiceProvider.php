<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Query logging untuk development
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::debug(
                    $query->sql,
                    [
                        'bindings' => $query->bindings,
                        'time' => $query->time
                    ]
                );
            });
        }

        // Cache invalidation untuk models
        $this->registerModelCacheInvalidation();
    }

    protected function registerModelCacheInvalidation(): void
    {
        // Definisi model dan key cache terkait
        $cacheKeys = [
            \App\Models\Product::class => ['active_products'],
            \App\Models\Order::class => ['recent_orders', 'order_stats'],
            \App\Models\Service::class => ['services_list', 'featured_services'],
        ];

        foreach ($cacheKeys as $model => $keys) {
            $model::saved(function ($instance) use ($keys) {
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
                // Tambahan untuk cache model-specific
                $this->forgetModelSpecificCache($instance);
            });

            $model::deleted(function ($instance) use ($keys) {
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
                // Tambahan untuk cache model-specific
                $this->forgetModelSpecificCache($instance);
            });
        }
    }

    /**
     * Hapus cache spesifik untuk model tertentu
     */
    protected function forgetModelSpecificCache($instance): void
    {
        if ($instance instanceof \App\Models\Product) {
            // Hapus cache untuk produk spesifik (e.g. product_slug)
            Cache::forget("product_{$instance->slug}");
        } elseif ($instance instanceof \App\Models\Order) {
            // Hapus cache untuk order spesifik
            Cache::forget("order_{$instance->id}");
        }
    }
} 