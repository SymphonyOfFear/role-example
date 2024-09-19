<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, int $roleId): Response
    {
        abort_if(auth()->user()->role_id !== $roleId, Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
