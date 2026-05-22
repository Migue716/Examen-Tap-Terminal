<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionAccess
{
    public function handle(Request $request, Closure $next, string $module, string $mode = 'read'): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $requiresWrite = $mode === 'write';

        if (! $user->canAccessSection($module, $requiresWrite)) {
            return response()->json(['message' => 'No tiene permiso para esta sección.'], 403);
        }

        return $next($request);
    }
}
