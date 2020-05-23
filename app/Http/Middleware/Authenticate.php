<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson() || $request->exists('api_token')) {
                \Log::info("entgfgfcgfra");
                return response(json_encode([
                    'response' => 'error',
                    'data' => 'No autorizado'
                    ]),401);
            } else {
                \Log::info("entra");
                return redirect()->guest('login');
            }
        }
\Log::info("entr2355a");
        return $next($request);
    }
}
