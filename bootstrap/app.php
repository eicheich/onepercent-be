<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use MongoDB\Driver\Exception\ConnectionTimeoutException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Fix: CORS harus di prepend API, bukan di alias
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->alias([
            'auth.web'   => \App\Http\Middleware\AuthWebMiddleware::class,
            'guest.web'  => \App\Http\Middleware\GuestWebMiddleware::class,
            'auth.admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return redirect('/');
        });

        $exceptions->render(function (ConnectionTimeoutException $e, Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }
            return response()->json([
                'status'     => 'error',
                'message'    => 'Database connection timeout.',
                'error_code' => 'DB_CONNECTION_TIMEOUT',
            ], 503);
        });
    })->create();
