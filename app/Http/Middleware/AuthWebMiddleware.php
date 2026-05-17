<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthWebMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('web_token')) {
            return redirect()->route('web.login');
        }
        return $next($request);
    }
}
