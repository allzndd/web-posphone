<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    // /**
    //  * A list of exception types with their corresponding custom log levels.
    //  *
    //  * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
    //  */
    // protected $levels = [
    //     //
    // ];

    // /**
    //  * A list of the exception types that are not reported.
    //  *
    //  * @var array<int, class-string<\Throwable>>
    //  */
    // protected $dontReport = [
    //     //
    // ];

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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Skip custom error pages for AJAX requests in debug mode
        if (config('app.debug') && $request->expectsJson()) {
            return parent::render($request, $e);
        }

        // Let Laravel handle validation exceptions natively (redirect back with errors)
        if ($e instanceof ValidationException) {
            return parent::render($request, $e);
        }

        // Let Laravel handle token mismatch (CSRF) exceptions natively
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            return parent::render($request, $e);
        }

        // Handle specific exceptions first
        if ($e instanceof NotFoundHttpException) {
            return response()->view('errors.404', ['exception' => $e], 404);
        }
        
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->view('errors.405', ['exception' => $e], 405);
        }

        // Handle HTTP exceptions
        if ($this->isHttpException($e)) {
            $statusCode = $e->getStatusCode();
            
            // Check if we have a custom error view for this status code
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [
                    'exception' => $e
                ], $statusCode);
            }
        }

        // Handle authentication exceptions
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        // For all other server errors, show custom 500 page
        // Only in debug mode for web requests, show custom page with debug info
        if (!$request->expectsJson()) {
            return response()->view('errors.500', ['exception' => $e], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
                ? response()->json(['message' => $exception->getMessage()], 401)
                : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
