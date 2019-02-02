<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Abonado;
use App\Http\Requests\AbonadoRequest;
use DB;

class AbonadoController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('secretaria');
    }

    public function getAbonados(Request $r){
        $total = DB::table('abonados')->where('deleted_at', null)->count();
        $abonados = Abonado::obtenerAbonados($r->desde, $r->cantidad, $r->columna, $r->orden);
        return response()->json(['ok'=> true, 'abonados' => $abonados, 'total' => $total], 200);
    }

    public function getAbonado(Request $request, $id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);
        }
        return response()->json(['ok'=> true, 'abonado' => $abonado], 200);
    }

    public function buscarAbonados(Request $r, $termino = ''){
        $abonados = Abonado::paginarAbonados($termino, $r->desde, $r->cantidad, $r->columna, $r->orden);
        $total = Abonado::buscarAbonado($termino)->count();
        return response()->json(['ok'=> true, 'abonados' => $abonados, 'total' => $total], 200);
    }

    public function postAbonado(AbonadoRequest $request){
        $abonado = new Abonado();
        $abonado->fill($request->all());
        $abonado->save();
        return response()->json(['ok' => true, 'abonado' => $abonado], 201);
    }

    public function putAbonado(Request $request, $id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);
        }
        $abonado->fill($request->all());
        $abonado->save();
        return response()->json(['ok' => true, 'message' => 'Abonado actualizado correctamente'], 201);
    }

    public function deleteAbonado($id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);
        }
        $abonado->delete();
        return response()->json(['ok' => true, 'message' => 'Abonado eliminado correctamente'], 201);
    }

}
