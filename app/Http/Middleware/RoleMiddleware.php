<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if the user is authenticated. If not, return an unauthorized response.
        if (! $request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Iterate through the provided roles.
        // If the user's role matches any of the allowed roles, proceed with the request.
        foreach ($roles as $role) {
            if ($request->user()->role === $role) {
                return $next($request);
            }
        }

        // If the user's role does not match any of the allowed roles, return a forbidden response.
        return response()->json(['message' => 'Unauthorized. You do not have the required role.'], 403);
    }
}
