<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Inside tenant context, users are admins. Check if logged in.
        if (!Auth::guard('web')->check()) {
            return redirect()->route('tenant.login');
        }

        return $next($request);
    }
}
