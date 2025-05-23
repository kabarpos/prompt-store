<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Throwable;
use App\Models\User;

class LoggingService
{
    /**
     * Log kesalahan aplikasi
     */
    public static function logError(Throwable $exception, array $context = []): void
    {
        $defaultContext = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
        ];

        Log::error($exception->getMessage(), array_merge($defaultContext, $context));
    }
    
    /**
     * Log aktivitas API
     */
    public static function logApiRequest(Request $request, $response, $startTime): void
    {
        $executionTime = microtime(true) - $startTime;
        $statusCode = $response->getStatusCode();
        
        $logLevel = 'info';
        if ($statusCode >= 400 && $statusCode < 500) {
            $logLevel = 'warning';
        } elseif ($statusCode >= 500) {
            $logLevel = 'error';
        }
        
        $logData = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => $request->user()?->id,
            'status_code' => $statusCode,
            'execution_time' => round($executionTime * 1000, 2) . 'ms',
            'user_agent' => $request->userAgent(),
        ];
        
        Log::channel('api')->$logLevel('API Request', $logData);
    }
    
    /**
     * Log aktivitas pengguna
     */
    public static function logUserActivity(User $user, string $action, array $details = []): void
    {
        Log::channel('user-activity')->info('User Activity: ' . $action, [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'details' => $details,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * Log performa eksekusi kode
     */
    public static function logPerformance(string $operation, float $startTime, array $context = []): void
    {
        $executionTime = microtime(true) - $startTime;
        
        // Log performance warning if execution time is too high
        $logLevel = $executionTime > 1.0 ? 'warning' : 'debug';
        
        Log::channel('performance')->$logLevel('Performance: ' . $operation, array_merge([
            'execution_time' => round($executionTime * 1000, 2) . 'ms',
        ], $context));
    }
    
    /**
     * Log akses halaman
     */
    public static function logPageAccess(Request $request): void
    {
        Log::channel('page-access')->info('Page Access', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
            'referer' => $request->header('referer'),
            'user_agent' => $request->userAgent(),
        ]);
    }
} 