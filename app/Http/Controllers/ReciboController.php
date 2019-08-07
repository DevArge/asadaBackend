<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recibo;
use App\Lectura;
use App\Medidor;
use App\Abonado;
use App\Historial;
use DB;

class ReciboController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth', ['except' => ['getRecibosAbonado', 'getRecibosAbonadoPendiente']]);
        $this->middleware('secretaria', ['only' => ['getCuentasRecibos', 'putRecibo', 'deleteRecibo']]);
    }

    public function getRecibos(Request $r){
        $formatoValido = Lectura::validarFormatoPeriodo($r->periodo);
        if (!$formatoValido) {
            return response()->json(['ok'=> false, 'message' => 'El formato del periodo es invalido'], 400);
        }
        Recibo::anadirImpuesto($r->periodo);
        $total = DB::table('recibos')->where('periodo', $r->periodo)->count();
        $recibos = Recibo::obtenerRecibos($r->desde, $r->cantidad, $r->columna, $r->orden, $r->periodo);
        return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total], 200);
    }

    public function getRecibosMedidor(Request $r, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El medidor con el ID: ' . $id . ' no existe'], 403);
        }
        $total = DB::table('recibos')->where('idMedidor', $id)->count();
        $recibos = Recibo::obtenerRecibosUnMedidor($r->desde, $r->cantidad, $r->columna, $r->orden, $id);
        return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total], 200);
    }

    public function getRecibosAbonado(Request $r, $id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);
        }
        $total = DB::table('recibos')->where('idAbonado', $id)->count();
        $recibos = Recibo::obtenerRecibosUnAbonado($r->desde, $r->cantidad, $r->columna, $r->orden, $id);
        return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total], 200);
    }

    public function getRecibosAbonadoPendiente(Request $r, $id){
        $abonado = Abonado::find($id);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'El abonado con el ID: ' . $id . ' no existe'], 403);
        }
        $total = DB::table('recibos')->where('idAbonado', $id)->where('estado', 'PENDIENTE')->count();
        $recibos = Recibo::obtenerRecibosUnAbonadoPendiente($r->desde, $r->cantidad, $r->columna, $r->orden, $id);
        return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total], 200);
    }

    public function getCuentasRecibos(Request $r){
        $formatoValido = Lectura::validarFormatoPeriodo($r->periodo);
        if (!$formatoValido) {
            return response()->json(['ok'=> false, 'message' => 'El formato del periodo es invalido'], 400);
        }
        $total = DB::table('recibos')->where('periodo', $r->periodo)->count();
        $cuentas = Recibo::calcularCuentas($r->periodo);
        $cuentasRecaudadas = Recibo::calcularCuentasRecaudadas($r->periodo);
        $recibos = Recibo::obtenerRecibos($r->desde, $r->cantidad, $r->columna, $r->orden, $r->periodo);
        return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total, 'cuentas' => $cuentas, 'cuentasRecaudadas' => $cuentasRecaudadas], 200);
    }

    public function buscarRecibos(Request $r,$tipo, $termino = ''){
        if ($tipo == 'id') {
            $recibo = Medidor::find($r->idMedidor);
            if (!$recibo) {
                return response()->json(['ok'=> false, 'message' => 'El medidor con el ID: ' . $r->idMedidor . ' no existe'], 403);
            }
            $total = Recibo::buscarRecibosUnmedidor($termino, $r->idMedidor)->count();
            $recibos = Recibo::buscarRecibosUnmedidor($termino, $r->idMedidor)->get();
            return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total], 200);
        }elseif($tipo == 'periodo'){
            if (!$r->periodo) {
                return response()->json(['ok'=> false, 'message' => 'Debe de mandar el periodo por parametro'], 400);
            }
            if (!Lectura::validarFormatoPeriodo($r->periodo)) {
                return response()->json(['ok'=> false, 'message' => 'El formato del periodo no es valido'], 400);
            }
            $total = Recibo::buscarRecibos($termino, $r->periodo, $r->desde, $r->cantidad, $r->columna, $r->orden)->count();
            $recibos = Recibo::buscarRecibos($termino, $r->periodo, $r->desde, $r->cantidad, $r->columna, $r->orden)->get();
            return response()->json(['ok'=> true, 'recibos' => $recibos, 'total' => $total], 200);
        }else{
            return response()->json(['ok'=> false, 'message' => 'El tipo: ' . $tipo . ' no es un tipo valido'], 400);
        }
    }

    public function putRecibo(Request $r, $id){
        $recibo = Recibo::find($id);
        if (!$recibo) {
            return response()->json(['ok'=> false, 'message' => 'El recibo con el ID: ' . $id . ' no existe'], 403);
        }
        if ($recibo->estodo === 'PAGADO') {
            return response()->json(['ok'=> false, 'message' => 'El recibo ya se encuentra pagado'], 400);
        }
        $recibo->estado = 'PAGADO';
        $recibo->save();
        $detalle = Recibo::toString($recibo, $recibo, true);
        Historial::crearHistorial('Pagó el recibo #' . $recibo->id, $detalle);
        return response()->json(['ok' => true, 'message' => 'Recibo ha sido pagado correctamente'], 201);
    }

    public function deleteRecibo($id){
        $recibo = Recibo::find($id);
        if (!$recibo) {
            return response()->json(['ok'=> false, 'message' => 'El recibo con el ID: ' . $id . ' no existe'], 403);
        }
        $reciboPosterior = Recibo::reciboPosterior($recibo->idMedidor, $recibo->periodo);
        if ($reciboPosterior != null) {
          return response()->json(['ok' => false, 'message' => 'No se puede eliminar un recibo cuando existe uno en un periodo posterior'], 200);
        }
        Recibo::devolverDeudas($recibo);
        $lectura = Lectura::find($recibo->idLectura);
        $recibo->delete();
        $lectura->delete();
        $detalle = Recibo::toString($recibo, $recibo, true);
        Historial::crearHistorial('Eliminó el recibo #' . $recibo->id, $detalle);
        return response()->json(['ok' => true, 'message' => 'Recibo eliminado correctamente'], 201);
    }

    public function putFechaVencimiento(Request $r){
      if (!$r->periodo) {
          return response()->json(['ok'=> false, 'message' => 'Debe de mandar el periodo por parametro'], 400);
      }
      if (!$r->vence) {
          return response()->json(['ok'=> false, 'message' => 'Debe de mandar la fecha de vencimiento por parametro'], 400);
      }
      Recibo::cambiarFechaVencimiento($r->periodo, $r->vence);
      Historial::crearHistorial('Cambió la fecha de vencimiento de los recibos del periodo: ' . $r->periodo, 'Ahora vencen el ' . $r->vence);
      return response()->json(['ok' => true, 'message' => 'Fecha actualizada correctamente'], 201);
    }

}
