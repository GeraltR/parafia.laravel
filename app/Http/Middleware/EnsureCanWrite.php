<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanWrite
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->permission_level->canWrite()) {
            abort(403, 'Brak uprawnień do zapisu zmian.');
        }

        return $next($request);
    }
}
