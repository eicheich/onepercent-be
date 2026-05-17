<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('web_token') || !session('is_admin')) {
            return redirect()->route('web.login');
        }
        return $next($request);
    }
}
