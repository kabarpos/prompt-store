<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LoggingService;

class TrackPageAccess
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
        // Proses request
        $response = $next($request);
        
        // Jangan log asset statis, gambar, atau file
        $skipExtensions = ['js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
        $pathInfo = pathinfo($request->path());
        
        if (!$request->expectsJson() && 
            !$request->ajax() && 
            $request->method() === 'GET' &&
            (!isset($pathInfo['extension']) || !in_array($pathInfo['extension'], $skipExtensions))) {
            
            // Log akses halaman
            LoggingService::logPageAccess($request);
        }
        
        return $response;
    }
} 