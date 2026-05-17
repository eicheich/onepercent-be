<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use \App\Http\Middleware\AuthWebMiddleware;
use \App\Http\Middleware\GuestWebMiddleware;
use \App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.web' => AuthWebMiddleware::class,
            'guest.web' => GuestWebMiddleware::class,
            'auth.admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect('/');
        });

        $exceptions->render(function (ConnectionTimeoutException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Database connection timeout. Please try again in a few moments.',
                'error_code' => 'DB_CONNECTION_TIMEOUT',
            ], 503);
        });
    })->create();
