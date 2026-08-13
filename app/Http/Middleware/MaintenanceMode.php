<?php
namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin*') || $request->is('login') || $request->is('logout') || $request->is('up')) return $next($request);
        try { $enabled = Setting::get('maintenance_mode','off') === 'on'; } catch (\Throwable $e) { $enabled = false; }
        if ($enabled && !($request->user()?->isAdmin())) return response()->view('errors.maintenance', [], 503)->header('Retry-After','900');
        return $next($request);
    }
}
