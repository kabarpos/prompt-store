<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LoggingService;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Catat waktu mulai eksekusi
        $startTime = microtime(true);
        
        // Proses request
        $response = $next($request);
        
        // Log request API
        LoggingService::logApiRequest($request, $response, $startTime);
        
        return $response;
    }
} 