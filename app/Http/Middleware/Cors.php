<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        $headers = [
            'Access-Control-Allow-Origin'      => !isset($_SERVER['HTTP_ORIGIN']) ? '*' : $_SERVER['HTTP_ORIGIN'],
            'Access-Control-Allow-Methods' => 'HEAD, POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            // 'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            // 'Access-Control-Allow-Headers' => 'X-Custom-Header, X-Requested-With, Content-Type, Origin, Authorization, Accept',
            'Access-Control-Allow-Headers' => 'Authorization, X-Authorization, Origin, Accept, Content-Type, X-Requested-With, X-HTTP-Method-Override',
                                               
        ];

        //Using this you don't need an method for 'OPTIONS' on controller
        if ($request->isMethod('OPTIONS')) {
            return Response::json('{"method":"OPTIONS"}', 200, $headers);
        }

        // For all other cases
        $response = $next($request);
        /*foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }*/
        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');

        return $response;
    }
}
