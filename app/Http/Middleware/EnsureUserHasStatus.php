<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$statuses
     */
    public function handle(Request $request, Closure $next, string ...$statuses): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasStatus(...$statuses)) {
            abort(Response::HTTP_FORBIDDEN, 'Vous ne disposez pas des droits requis pour accéder à cette ressource.');
        }

        return $next($request);
    }
}
