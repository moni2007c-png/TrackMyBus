<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(trim((string) Auth::user()->role));
        $requiredRole = strtolower(trim($role));

        if ($userRole !== $requiredRole) {
            abort(
                403,
                'Unauthorized action. Your account does not have permission to access this page.'
            );
        }

        return $next($request);
    }
}