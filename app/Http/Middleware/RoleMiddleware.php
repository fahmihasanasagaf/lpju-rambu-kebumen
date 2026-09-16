<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memeriksa apakah user memiliki salah satu role
     * yang diizinkan oleh route.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        // Pastikan user sudah login.
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Pastikan role user termasuk role yang diizinkan.
        if (!in_array($request->user()->role, $roles, true)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengakses endpoint ini.',
            ], 403);
        }

        return $next($request);
    }
}