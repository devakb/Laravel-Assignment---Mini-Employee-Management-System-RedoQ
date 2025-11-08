<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->header('X-ROLE');

        if ($role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        return $next($request);
    }
}

