<?php
namespace App\Http\Middleware;
use App\Models\ApiKey;use Closure;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\Response;
class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $token=$request->bearerToken();
        if(!$token) return response()->json(['message'=>'Missing bearer API key.'],401);
        $key=ApiKey::where('key_hash',hash('sha256',$token))->first();
        if(!$key || !$key->is_active) return response()->json(['message'=>'Invalid, expired or revoked API key.'],401);
        if(!in_array($ability,$key->abilities??[],true)) return response()->json(['message'=>'API key does not have '.$ability.' permission.'],403);
        $key->forceFill(['last_used_at'=>now()])->save();
        $request->attributes->set('trendora_api_key',$key);
        return $next($request);
    }
}
