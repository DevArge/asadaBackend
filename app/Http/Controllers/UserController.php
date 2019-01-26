<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use DB;
use Hash;

class UserController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('admin');
    }

    public function getUsers(Request $r){
        $total = DB::table('users')->where('deleted_at', null)->count();
        $usuarios = User::obtenerUsers($r->desde, $r->cantidad, $r->columna, $r->orden);
        return response()->json(['ok'=> true, 'usuarios' => $usuarios, 'total' => $total], 200);
    }

    public function getUser(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['ok'=> false, 'message' => 'El usuario con el ID: ' . $id . ' no existe'], 403);        
        }
        return response()->json(['ok'=> true, 'usuario' => $user], 200);
    }

    public function buscarUsers(Request $r, $termino = ''){
        $usuarios = User::paginarUsuarios($termino, $r->desde, $r->cantidad, $r->columna, $r->orden);
        $total = User::buscarUsuario($termino)->count();
        return response()->json(['ok'=> true, 'usuarios' => $usuarios, 'total' => $total], 200);
    }

    public function postUser(UserRequest $request){
        $usuario = new User();
        $usuario->fill($request->all());
        $usuario->password = Hash::make($request->password);
        $usuario->idAsada = DB::table('asadas')->where('id', '>=', 1)->value('id');
        $usuario->save();
        return response()->json(['ok' => true, 'usuario' => $usuario], 201);
    }

    public function putUser(UserUpdateRequest $request, $id){
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['ok'=> false, 'message' => 'El usuario con el ID: ' . $id . ' no existe'], 403);        
        }
        $usuario->fill($request->all());
        $usuario->password = Hash::make($request->password);
        $usuario->save();
        return response()->json(['ok' => true, 'message' => 'Usuario actualizado correctamente'], 201);
    }

    public function deleteUser($id){
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['ok'=> false, 'message' => 'El usuario con el ID: ' . $id . ' no existe'], 403);        
        }
        $usuario->delete();
        return response()->json(['ok' => true, 'message' => 'Usuario eliminado correctamente'], 201);
    }
    
}
