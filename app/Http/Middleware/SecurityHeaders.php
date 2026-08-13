<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response=$next($request);
        $response->headers->set('X-Frame-Options','DENY');
        $response->headers->set('X-Content-Type-Options','nosniff');
        $response->headers->set('Referrer-Policy','strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy','geolocation=(), microphone=(), camera=(), payment=(self)');
        $csp="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none';";
        if($request->isSecure()){ $csp.=' upgrade-insecure-requests;'; $response->headers->set('Strict-Transport-Security','max-age=31536000; includeSubDomains'); }
        $response->headers->set('Content-Security-Policy',$csp);
        if($request->is('admin*') || $request->is('checkout*') || $request->is('account*') || $request->is('profile*')) $response->headers->set('Cache-Control','no-store, private');
        return $response;
    }
}
