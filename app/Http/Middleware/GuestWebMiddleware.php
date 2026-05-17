<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestWebMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (session('web_token')) {
            return redirect()->route('web.dashboard');
        }
        return $next($request);
    }
}
