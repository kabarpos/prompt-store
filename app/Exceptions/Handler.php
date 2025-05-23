<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use Throwable;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                app('sentry')->captureException($e);
            }
        });
        
        // Handle specific exceptions with custom responses
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource tidak ditemukan',
                ], 404);
            }
            
            if (app()->environment('production')) {
                return Inertia::render('Errors/NotFound');
            }
        });
        
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Data yang dicari tidak ditemukan',
                ], 404);
            }
            
            if (app()->environment('production')) {
                return Inertia::render('Errors/NotFound');
            }
        });
        
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk mengakses resource ini',
                ], 403);
            }
            
            if (app()->environment('production')) {
                return Inertia::render('Errors/Forbidden');
            }
        });
        
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Silahkan login terlebih dahulu',
                ], 401);
            }
        });
        
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Data yang diberikan tidak valid',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
        
        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi telah berakhir. Silahkan refresh halaman.',
                ], 419);
            }
            
            return redirect()->back()
                ->withInput($request->except($this->dontFlash))
                ->with('error', 'Sesi telah berakhir. Silahkan coba lagi.');
        });
        
        $this->renderable(function (QueryException $e, $request) {
            // Log the database error
            logger()->error('Database error: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'Unknown SQL',
                'bindings' => $e->getBindings() ?? [],
                'code' => $e->getCode(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada database',
                ], 500);
            }
            
            if (app()->environment('production')) {
                return Inertia::render('Errors/ServerError');
            }
        });
        
        $this->renderable(function (HttpException $e, $request) {
            $status = $e->getStatusCode();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Terjadi kesalahan pada server',
                ], $status);
            }
            
            if (app()->environment('production') && in_array($status, [500, 503])) {
                return Inertia::render('Errors/ServerError');
            }
        });
        
        // Fallback for all other exceptions
        $this->renderable(function (Throwable $e, $request) {
            if (!app()->environment('production')) {
                return null; // Let Laravel handle it in non-production environments
            }
            
            logger()->error('Unhandled exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada server',
                ], 500);
            }
            
            return Inertia::render('Errors/ServerError');
        });
    }
} 