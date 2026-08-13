<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // This middleware is intentionally limited to authenticated admin mutations.
        if (!$request->is('admin/*') || !$request->user()?->isAdmin()) return $response;
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) return $response;

        $route = $request->route();
        if (!$route || in_array($route->getName(), ['admin.login.submit', 'logout'], true)) return $response;

        $name = (string) $route->getName();
        $parts = explode('.', $name);
        $module = $parts[1] ?? 'admin';

        try {
            ActivityLog::log(
                $request->method(),
                $module,
                $request->method().' request on '.$route->uri(),
                [
                    'route' => $name,
                    'url' => $request->fullUrl(),
                    'input' => $this->redact($request->all()),
                ]
            );
        } catch (\Throwable $e) {
            // Audit logging must never break the commerce request itself.
        }

        return $response;
    }

    private function redact(mixed $value, ?string $key = null): mixed
    {
        if ($key && preg_match('/password|secret|token|api[_-]?key|private[_-]?key|stripe[_-]?key|client[_-]?secret|authorization|cvv|cvc|card[_-]?number|integrity[_-]?salt/i', $key)) {
            return '[REDACTED]';
        }
        if (!is_array($value)) return $value;

        $clean = [];
        foreach ($value as $childKey => $childValue) {
            $clean[$childKey] = $this->redact($childValue, (string) $childKey);
        }
        return $clean;
    }
}
