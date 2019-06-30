<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cuenta;
use App\Historial;
use App\Http\Requests\CuentaRequest;
use DB;

class CuentasController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('secretaria');
    }

    public function getCuentas(Request $r){
        $total = DB::table('cuentas')->where('deleted_at', null)->count();
        $cuentas = Cuenta::obtenerCuentas($r->desde, $r->cantidad, $r->columna, $r->orden);
        return response()->json(['ok'=> true, 'cuentas' => $cuentas, 'total' => $total], 200);
    }

    public function getCuenta(Request $request, $id){
        $cuenta = Cuenta::find($id);
        if (!$cuenta) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        return response()->json(['ok'=> true, 'cuenta' => $cuenta], 200);
    }

    public function getCuentasAll(){
        $cuentas = Cuenta::all();
        return response()->json(['ok'=> true, 'cuentas' => $cuentas], 200);
    }

    public function buscarCuentas(Request $r, $termino = ''){
        $abonados = Cuenta::paginarCuentas($termino, $r->desde, $r->cantidad, $r->columna, $r->orden);
        $total = Cuenta::buscarCuenta($termino)->count();
        return response()->json(['ok'=> true, 'cuentas' => $abonados, 'total' => $total], 200);
    }

    public function postCuenta(CuentaRequest $request){
        $cuenta = new Cuenta();
        $idAsada = DB::table('asadas')
                    ->where('id', '>=', 1)
                    ->value('id');
        $cuenta->fill($request->all());
        $cuenta->idAsada = $idAsada;
        $cuenta->save();
        $detalle = Cuenta::toString($cuenta, $cuenta, true);
        Historial::crearHistorial('Creo la Cuenta ' . $cuenta->nombre, $detalle);
        return response()->json(['ok' => true, 'cuenta' => $cuenta], 201);
    }

    public function putCuenta(Request $request, $id){
        $cuenta = Cuenta::find($id);
        $original = Cuenta::find($id);
        if (!$cuenta) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $cuenta->fill($request->all());
        $cuenta->save();
        $detalle = Cuenta::toString($original, $cuenta);
        Historial::crearHistorial('Actualizó la Cuenta ' . $original->nombre, $detalle);
        return response()->json(['ok' => true, 'message' => 'Cuenta actualizada correctamente'], 201);
    }

    public function deleteCuenta($id){
        $cuenta = Cuenta::find($id);
        if (!$cuenta) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $cuenta->delete();
        $detalle = Cuenta::toString($cuenta, $cuenta, true);
        Historial::crearHistorial('Eliminó la Cuenta ' . $cuenta->nombre, $detalle);
        return response()->json(['ok' => true, 'message' => 'Cuenta eliminada correctamente'], 201);
    }
}
