<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectOneciToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }
        
        // Si l'utilisateur est un agent ONECI
        if ($user->role === 'oneci') {
            // Autoriser uniquement les routes ONECI et logout
            if ($request->routeIs('oneci.*') || $request->routeIs('logout')) {
                return $next($request);
            }
            // Bloquer toutes les autres routes et rediriger vers le dashboard ONECI
            return redirect()->route('oneci.dashboard');
        }
        
        // Si l'utilisateur n'est pas ONECI et essaie d'accéder aux routes ONECI
        if ($request->routeIs('oneci.*')) {
            abort(403, 'Accès réservé aux agents ONECI');
        }

        return $next($request);
    }
}
