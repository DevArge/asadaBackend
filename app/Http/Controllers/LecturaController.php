<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lectura;
use App\Medidor;
use App\Recibo;
use DB;
//use Carbon\Carbon;

class LecturaController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function getLecturas(Request $r){
        $total = DB::table('lecturas')->where('periodo', $r->periodo)->count();
        $lecturas = Lectura::obtenerLecturas($r->desde, $r->cantidad, $r->columna, $r->orden, $r->periodo);
        return response()->json(['ok'=> true, 'lecturas' => $lecturas, 'total' => $total], 200);
    }

    public function getInsertarLecturas(Request $r){
      $total = DB::table('medidores')->where('estado', 'ACTIVO')->count();
      $sinRegistrar = $total - DB::table('lecturas')->where('periodo', $r->periodo)->count();
      $lecturas = Lectura::obtenerInsertarLecturas($r->desde, $r->cantidad, $r->columna, $r->orden, $r->periodo);
      return response()->json(['ok'=> true, 'lecturas' => $lecturas, 'total' => $total, 'sinRegistrar' => $sinRegistrar], 200);
    }

    public function getLecturasMedidor(Request $r, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El medidor con el ID: ' . $id . ' no existe'], 403);
        }
        $lecturas = Lectura::obtenerLecturasDeUnMedidor($r->desde, $r->cantidad, $r->columna, $r->orden, $id);
        $total = DB::table('lecturas')->where('idMedidor', $id)->count();
        return response()->json(['ok'=> true, 'lecturas' => $lecturas, 'total' => $total], 200);
    }

    public function buscarLecturas(Request $r, $tipo, $termino = ''){
        if ($tipo == 'id') {
            $medidor = Medidor::find($r->idMedidor);
            if (!$medidor) {
                return response()->json(['ok'=> false, 'message' => 'El medidor con el ID: ' . $r->idMedidor . ' no existe'], 403);
            }
            $total = Lectura::buscarLecturasUnmedidor($termino, $r->idMedidor)->count();
            $lecturas = Lectura::buscarLecturasUnmedidor($termino, $r->idMedidor)->get();
            return response()->json(['ok'=> true, 'lecturas' => $lecturas, 'total' => $total], 200);
        }elseif($tipo == 'periodo'){
            if (!$r->periodo) {
                return response()->json(['ok'=> false, 'message' => 'Debe de mandar el periodo por parametro'], 400);
            }
            if (!Lectura::validarFormatoPeriodo($r->periodo)) {
                return response()->json(['ok'=> false, 'message' => 'El formato del periodo no es valido'], 400);
            }
            if ($r->tipo == 'insertar') {
              $total = Lectura::buscarLecturasInsertar($termino, $r->periodo, $r->desde, $r->cantidad, $r->columna, $r->orden)->count();
              $lecturas = Lectura::buscarLecturasInsertar($termino, $r->periodo, $r->desde, $r->cantidad, $r->columna, $r->orden)->get();
            }else{
              $total = Lectura::buscarLecturas($termino, $r->periodo, $r->desde, $r->cantidad, $r->columna, $r->orden)->count();
              $lecturas = Lectura::buscarLecturas($termino, $r->periodo, $r->desde, $r->cantidad, $r->columna, $r->orden)->get();
            }
            return response()->json(['ok'=> true, 'lecturas' => $lecturas, 'total' => $total], 200);
        }else{
            return response()->json(['ok'=> false, 'message' => 'El tipo: ' . $tipo . ' no es un tipo valido'], 400);
        }
    }

    public function postLectura(Request $request){
        $formatoValido = Lectura::validarFormatoPeriodo($request->periodo);
        if (!$formatoValido) {
            return response()->json(['ok'=> false, 'message' => 'El formato del periodo es invalido'], 400);
        }
        $medidor = Medidor::find($request->idMedidor);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        if ($medidor->estado == 'INACTIVO') {
            return response()->json(['ok'=> false, 'message' => 'No se puede insertar una lectura en un medidor con estado INACTIVO'], 400);
        }
        $mensaje = Lectura::validarLectura($request->lectura, $request->idMedidor, $request->periodo);
        if ($mensaje != 'actualizar lectura' && $mensaje != 'nueva lectura') {
            return response()->json(['ok' => false, 'message' =>  $mensaje], 400);
        }
        $lectura = Lectura::guardarLectura($request->lectura, $request->idMedidor, $request->periodo, $request->nota, $mensaje);
        return response()->json(['ok' => true, 'lectura' =>  $lectura], 201);
    }

    public function deleteLectura($id){
        $lectura = Lectura::find($id);
        if (!$lectura) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $mensaje = Lectura::validarLectura($lectura->lectura, $lectura->idMedidor, $lectura->periodo);
        if ($mensaje != 'actualizar lectura' && $mensaje != 'nueva lectura') {
            return response()->json(['ok' => false, 'message' =>  $mensaje], 400);
        }
        $recibo = Recibo::where('idLectura', $id)->first();
        Recibo::devolverDeudas($recibo);
        $recibo->delete();
        $lectura->delete();
        return response()->json(['ok' => true, 'message' => 'Lectura eliminada correctamente'], 201);
    }

}
