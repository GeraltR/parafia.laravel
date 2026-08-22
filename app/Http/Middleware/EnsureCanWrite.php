<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanWrite
{
    public function handle(Request $request, Closure $next, string $area): Response
    {
        $level = $request->user()?->permission_level;

        $canWrite = match ($area) {
            'site' => $level?->canWriteSite(),
            'content' => $level?->canWriteContent(),
            'management' => $level?->canWriteManagement(),
            default => false,
        };

        if (! $canWrite) {
            abort(403, 'Brak uprawnień do zapisu zmian.');
        }

        return $next($request);
    }
}
