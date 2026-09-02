<?php

namespace Modules\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
