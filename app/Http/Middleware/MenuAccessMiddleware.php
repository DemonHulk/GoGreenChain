<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate; // Asegúrate de importar Gate

class MenuAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $ability)
    {
        if (auth()->user() && !Gate::allows($ability)) {
            abort(403); // Acceso no autorizado
        }

        return $next($request);
    }

    
}
