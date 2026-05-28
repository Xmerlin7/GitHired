<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'UnAuthorized Please login first'
            ], 401);
        }

        $userRole = $request->user()->role->value ?? $request->user()->role;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، ليس لديك الصلاحية للقيام بهذا الإجراء.'
            ], 403); // 403 Forbidden
        }

        return $next($request);
    }
}
