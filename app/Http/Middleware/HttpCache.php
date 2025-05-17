<?php

namespace App\Http\Middleware;

use App\Services\CacheManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpCache
{
    /**
     * Cache manager instance
     */
    protected CacheManager $cacheManager;
    
    /**
     * Create a new middleware instance
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }
    
    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lewati cache untuk endpoint Inertia
        if ($request->header('X-Inertia')) {
            return $next($request);
        }
        
        // Cek apakah response untuk request ini sudah ada di cache
        if ($this->cacheManager->has($request)) {
            $cachedData = $this->cacheManager->get($request);
            
            // Build response dari cache data
            $response = new Response(
                $cachedData['content'],
                $cachedData['status'] ?? 200
            );
            
            // Tambahkan headers dari cache
            foreach ($cachedData['headers'] ?? [] as $key => $values) {
                foreach ($values as $value) {
                    $response->headers->set($key, $value, false);
                }
            }
            
            // Tambahkan X-Cache header untuk debugging
            $response->headers->set('X-Cache', 'HIT');
            
            return $response;
        }
        
        // Proses request dan dapatkan response
        $response = $next($request);
        
        // Cache response jika memenuhi syarat
        if ($response instanceof Response) {
            $this->cacheManager->put($request, $response);
            
            // Tambahkan X-Cache header untuk debugging
            $response->headers->set('X-Cache', 'MISS');
        }
        
        return $response;
    }
} 