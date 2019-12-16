<?php

namespace App\Http\Middleware;

use Closure;

//Middleware metodo que se ejecuta antes del controlador, actua como filtro

class ApiAuthMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if ($checkToken) {
            return $next($request);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El Usuario no esta identificado correctamente.'
            );
            return response()->json($data,$data['code']);
        }
    }

}
