<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller{

    public function getUsers(Request $r){
        $total = DB::table('users')->where('deleted_at', null)->count();
        $abonados = User::obtenerAbonados($r->desde, $r->cantidad, $r->columna, $r->orden);
        return response()->json(['ok'=> true, 'abonados' => $abonados, 'total' => $total], 200);
    }

    public function getUser(Request $request, $id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);        
        }
        return response()->json(['ok'=> true, 'abonado' => $abonado], 200);
    }

    public function buscarUsers(Request $r, $termino = ''){
        $abonados = Abonado::paginarAbonados($termino, $r->desde, $r->cantidad, $r->columna, $r->orden);
        $total = Abonado::buscarAbonado($termino)->count();
        return response()->json(['ok'=> true, 'abonados' => $abonados, 'total' => $total], 200);
    }

    public function postUser(AbonadoRequest $request){
        $abonado = new Abonado();
        $abonado->fill($request->all());
        $abonado->save();
        return response()->json(['ok' => true, 'abonado' => $abonado], 201);
    }

    public function putUser(AbonadoRequest $request, $id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);        
        }
        $abonado->fill($request->all());
        $abonado->save();
        return response()->json(['ok' => true, 'message' => 'Abonado actualizado correctamente'], 201);
    }

    public function deleteUser($id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);        
        }
        $abonado->delete();
        return response()->json(['ok' => true, 'message' => 'Abonado eliminado correctamente'], 201);
    }
    
}
