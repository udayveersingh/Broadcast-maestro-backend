<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Force JSON for API calls
        if ($request->expectsJson() || $request->is('api/*')) {
            // Validation error handling
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $exception->errors(),
                ], 422);
            }

            // Generic error response
            $status = ($exception instanceof HttpExceptionInterface)
                ? $exception->getStatusCode()
                : 500;

            return response()->json([
                'message' => $exception->getMessage(),
                'trace' => config('app.debug') ? $exception->getTrace() : [],
            ], $status);
        }

        return parent::render($request, $exception);
    }

}
