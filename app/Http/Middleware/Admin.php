<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::getPayload($token)->toArray();
        if ($newToken['role'] != 'ADMIN_ROLE') {
            return response()->json(['ok' => false, 'message' => 'No posee permisos de ADMINISTRADOR'], 401);
        }
        return $next($request);
    }
}
