<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin() || !$user->hasAdminPermission($permission)) {
            abort(403, 'Your admin role does not have permission for this area.');
        }
        return $next($request);
    }
}
