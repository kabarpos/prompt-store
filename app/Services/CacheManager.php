<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CacheManager
{
    /**
     * Default cache TTL in seconds (1 hour)
     */
    protected int $defaultTtl = 3600;

    /**
     * Cek apakah response untuk request ini ada di cache
     */
    public function has(Request $request): bool
    {
        if (!$this->shouldCache($request)) {
            return false;
        }

        $key = $this->generateCacheKey($request);
        return Cache::has($key);
    }

    /**
     * Ambil response dari cache
     */
    public function get(Request $request)
    {
        $key = $this->generateCacheKey($request);
        return Cache::get($key);
    }

    /**
     * Simpan response ke cache
     */
    public function put(Request $request, SymfonyResponse $response, ?int $ttl = null): void
    {
        if (!$this->shouldCache($request, $response)) {
            return;
        }

        $key = $this->generateCacheKey($request);
        $ttl = $ttl ?? $this->defaultTtl;

        try {
            $data = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $this->serializeHeaders($response),
            ];

            Cache::put($key, $data, $ttl);
            
            // Log for debugging during development
            if (config('app.debug')) {
                Log::debug("Response cached: {$key}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to cache response: {$e->getMessage()}");
        }
    }

    /**
     * Hapus cache untuk request spesifik
     */
    public function forget(Request $request): void
    {
        $key = $this->generateCacheKey($request);
        Cache::forget($key);
    }

    /**
     * Hapus cache berdasarkan pola
     */
    public function forgetPattern(string $pattern): void
    {
        try {
            $cacheStore = Cache::getStore();
            
            // Periksa apakah memiliki metode keys() (Redis)
            if (method_exists($cacheStore, 'keys')) {
                $keys = $cacheStore->keys($pattern);
                
                foreach ($keys as $key) {
                    // Ambil identifier key (tanpa prefix)
                    $keyWithoutPrefix = str_replace(config('cache.prefix') . ':', '', $key);
                    Cache::forget($keyWithoutPrefix);
                }
            } else {
                // Fallback jika driver cache tidak mendukung pencarian pola
                Log::info("Cache driver tidak mendukung penghapusan berdasarkan pola. Cache mungkin tidak terhapus sepenuhnya.");
            }
        } catch (\Exception $e) {
            Log::error("Gagal menghapus cache dengan pola: {$pattern}. Error: {$e->getMessage()}");
        }
    }

    /**
     * Generate cache key berdasarkan request
     */
    protected function generateCacheKey(Request $request): string
    {
        $url = $request->fullUrl();
        $method = $request->method();
        return "page_cache.{$method}.".md5($url);
    }

    /**
     * Cek apakah request boleh di-cache
     */
    protected function shouldCache(Request $request, ?SymfonyResponse $response = null): bool
    {
        // Hanya cache request GET
        if (!$request->isMethod('GET')) {
            return false;
        }

        // Jangan cache request dengan query parameter _cache=no
        if ($request->query('_cache') === 'no') {
            return false;
        }

        // Jangan cache jika user authenticated (untuk development)
        if ($request->user()) {
            return false;
        }

        // Jangan cache respons Inertia.js (halaman SPA)
        if ($response && $this->isInertiaResponse($response)) {
            return false;
        }

        // Cek status response jika response tersedia
        if ($response && !$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah respons adalah respons Inertia.js
     */
    protected function isInertiaResponse(SymfonyResponse $response): bool
    {
        return $response->headers->has('X-Inertia') || 
               ($response->headers->has('Content-Type') && 
                str_contains($response->headers->get('Content-Type'), 'application/json'));
    }

    /**
     * Serialize response headers untuk disimpan di cache
     */
    protected function serializeHeaders(SymfonyResponse $response): array
    {
        $headers = [];
        foreach ($response->headers->all() as $key => $values) {
            $headers[$key] = $values;
        }
        
        // Filter headers yang tidak perlu di-cache
        unset($headers['set-cookie']);
        
        return $headers;
    }
} 