<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant) {
            // Check if tenant is suspended
            if ($tenant->status === 'suspended') {
                if (!$request->routeIs('tenant.logout')) {
                    \Illuminate\Support\Facades\Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('tenant.login')->withErrors([
                        'email' => 'Votre espace de travail a été suspendu par l\'administrateur. Veuillez contacter le support.',
                    ]);
                }
            }

            // Check if subscription is active
            if (!$tenant->isSubscriptionActive()) {
                // Check if user is trying to access routes other than the payment and logout routes
                if (!$request->routeIs('tenant.subscription.pay') && 
                    !$request->routeIs('tenant.subscription.store') && 
                    !$request->routeIs('tenant.logout')) {
                    return redirect()->route('tenant.subscription.pay');
                }
            }
        }

        return $next($request);
    }
}
