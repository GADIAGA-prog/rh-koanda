<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pour les RH multi-filiales : mémorise la filiale active en session.
 * À enregistrer dans bootstrap/app.php sous l'alias 'filiale.active'.
 */
class FilialeActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! session()->has('filiale_active') && $user->filiale_id) {
            session(['filiale_active' => $user->filiale_id]);
        }

        return $next($request);
    }
}
