<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Izinkan akses hanya untuk role tertentu.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ($request->user()?->role?->value !== null
            && in_array($request->user()->role->value, $roles, true)) {
            return $next($request);
        }

        abort(403);
    }
}
