<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MedidorRequest;
use App\Medidor;
use App\Abonado;
use App\Lectura;
use App\Historial;
use App\DeudaDeMedidor;
use DB;

class MedidorController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function getMedidores(Request $r){
        $total = $r->todos ? DB::table('medidores') : DB::table('medidores')->where('estado', 'ACTIVO');
        $medidores = Medidor::obtenerMedidores($r->desde, $r->cantidad, $r->columna, $r->orden, $r->todos);
        return response()->json(['ok'=> true, 'medidores' => $medidores, 'total' => $total->count()], 200);
    }

    public function getMedidor(Request $request, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El medidor con el ID: ' . $id . ' no existe'], 403);
        }
        return response()->json(['ok'=> true, 'medidor' => $medidor], 200);
    }

    public function buscarMedidores(Request $r, $termino = ''){
        $medidores = Medidor::paginarMedidores($termino, $r->desde, $r->cantidad, $r->columna, $r->orden, $r->todos);
        $total = Medidor::buscarMedidor($termino, $r->todos)->count();
        return response()->json(['ok'=> true, 'medidores' => $medidores, 'total' => $total], 200);
    }

    public function postMedidor(MedidorRequest $request){
        if ($request->plazo <= 0) {
            return response()->json(['ok'=> false, 'message' => 'El plazo tiene que ser mayor a 0'], 400);
        }
        $abonado = Abonado::find($request->idAbonado);
        if (!$abonado) {
            return response()->json(['ok'=> false, 'message' => 'No se le puede asignar un medidor a un Abonado inactivo'], 400);
        }
        $medidor = new Medidor();
        $medidor->fill($request->all());
        $medidor->estado = 'ACTIVO';
        $medidor->save();
        DeudaDeMedidor::guardar($request, $medidor->id);
        $detalle = Medidor::toString($medidor, $medidor, true);
        Historial::crearHistorial('Creo el medidor #' . $medidor->id, $detalle);
        return response()->json(['ok' => true, 'medidor' => $medidor], 201);
    }

    public function putMedidor(Request $request, $id){
        if (!$request->idTipoDeMedidor) {
            return response()->json(['ok'=> false, 'message' => 'El idTipoDeMedidor es obligatorio'], 400);
        }
        $medidor = Medidor::find($id);
        $original = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $medidor->idTipoDeMedidor = $request->idTipoDeMedidor;
        $medidor->detalle = $request->detalle ? $request->detalle : null;
        $medidor->save();
        $detalle = Medidor::toString($original, $medidor);
        Historial::crearHistorial('Actualizó el medidor #' . $medidor->id , $detalle);
        return response()->json(['ok' => true, 'message' => 'Medidor actualizado correctamente'], 201);
    }

    public function deleteMedidor($id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $medidor->estado = 'INACTIVO';
        $medidor->save();
        $detalle = Medidor::toString($medidor, $medidor, true);
        Historial::crearHistorial('Inhablilitó  el medidor #' . $medidor->id, $detalle);
        return response()->json(['ok' => true, 'message' => 'Medidor inactivado correctamente'], 201);
    }

    public function habilitarMedidor(Request $request, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        if ($medidor->estado == 'ACTIVO') {
            return response()->json(['ok'=> false, 'message' => 'No se puede activar un medidor que ya esta activado'], 400);
        }
        $medidor->estado = 'ACTIVO';
        $medidor->save();
        $costoActivacion = DB::table('configuracion_de_medidores')->where('id', '>=', 1)->value('impuestoReactivacion');
        $request->request->add(['plazo' => 1, 'tipoDeuda' =>'REACTIVACION', 'costoTotal' => $costoActivacion]);
        DeudaDeMedidor::guardar($request, $medidor->id);
        $detalle = Medidor::toString($medidor, $medidor, true);
        Historial::crearHistorial(' Habilitó el medidor #' . $medidor->id, $detalle);
        return response()->json(['ok' => true, 'message' => 'Se ha reactivado el medidor correctamente'], 201);
    }

}
