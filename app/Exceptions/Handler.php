<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            // Enhanced error logging for production
            if (app()->environment('production')) {
                $this->logProductionError($e);
            }
        });
    }

    /**
     * Log detailed error information for production debugging
     */
    protected function logProductionError(Throwable $e): void
    {
        $context = [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        // Add authenticated user info if available
        if (auth()->check()) {
            $context['user'] = [
                'id' => auth()->id(),
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ];
        }

        // Add request data (excluding sensitive fields)
        $context['request_data'] = request()->except([
            'password',
            'password_confirmation',
            'current_password',
            'api_key',
            'token',
        ]);

        Log::error('Production Error: ' . $e->getMessage(), $context);
    }
}
