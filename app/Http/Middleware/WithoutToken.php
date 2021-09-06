<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WithoutToken
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->bearerToken()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthenticated.'], 401);
    }
}
