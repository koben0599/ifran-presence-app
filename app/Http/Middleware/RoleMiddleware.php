<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Gère l'accès selon le(s) rôle(s) passé(s) en paramètre de la route.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Accès refusé');
        }
        return $next($request);
    }
}
