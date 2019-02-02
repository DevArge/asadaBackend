<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTException;

class LoginController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth',['only' => ['renuevaToken', 'deslogear']]);
    }

    public function login() {
        $credenciales = request(['email', 'password']);
        if (!$token = auth('api')->attempt($credenciales)) {
            return response()->json(['error' => 'usuario o contraseña incorrectos'], 401);
        }
        return response()->json([
            'ok' => true,
            'token' => $token,
            'expires' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function logout(Request $request){
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            return response()->json(['ok' => true, 'message'=> "Te has deslogueado de forma existosa"]);
        } catch (JWTException $e) {
            return response()->json(['ok' => false, 'error' => 'Error al desloguear, intenta nuevamente'], 500);
        }
    }

    public function renuevaToken(){
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);
        // $newToken = JWTAuth::getPayload($token)->toArray();
        return response()->json([
            'ok' => true,
            'token' => $newToken,
            'expires' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

}
