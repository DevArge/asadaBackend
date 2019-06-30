<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medidor;
use App\DeudaDeMedidor;
use App\Historial;
use DB;

class DeudaController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('secretaria');
    }

    public function getDeudas(Request $r, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $deudas = DeudaDeMedidor::paginar($r->desde, $r->cantidad, $r->columna, $r->orden, $id)->get();
        $total = DeudaDeMedidor::getDeudas($id)->count();
        return response()->json(['ok' => true, 'deudas' => $deudas, 'total' => $total], 200);
    }

    public function postReparacion(Request $request, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        if (!$request->tipoDeuda || !$request->costoTotal ) {
            return response()->json(['ok'=> false, 'message' => 'El tipoDeuda y el costoTotal son obligatorios'], 400);
        }
        if ($request->tipoDeuda != 'REPARACION') {
            return response()->json(['ok'=> false, 'message' => 'El tipoDeuda debe ser REPARACION'], 400);
        }
        if ($medidor->estado != 'ACTIVO') {
            return response()->json(['ok'=> false, 'message' => 'No se puede añadir una reparación a un medidor INACTIVO'], 400);
        }
        $request->request->add(['plazo' => 1]);
        $reparacion = DeudaDeMedidor::guardar($request, $medidor->id);
        $detalle = DeudaDeMedidor::toString($reparacion, $reparacion, true);
        Historial::crearHistorial('Añadio una reparación al medidor #' . $medidor->id, $detalle);
        return response()->json(['ok' => true, 'message' => 'Costo de reparacion añadida correctamente'], 201);
    }

    public function putDeuda(Request $request, $id){
        $deuda = DeudaDeMedidor::find($id);
        if (!$deuda) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);
        }
        $multiplo = DeudaDeMedidor::validarMultiplo($deuda, $request->deuda);
        if (!$multiplo) {
            return response()->json(['ok'=> false, 'message' => 'la deuda tiene que ser multiplo del total de la deuda divido entre: ' . $deuda->plazo . ' o cero'], 400);
        }
        $newDeuda = DeudaDeMedidor::actualizar($deuda->idMedidor, $request->deuda, $deuda->tipoDeuda);
        $detalle = DeudaDeMedidor::toString($deuda, $newDeuda);
        Historial::crearHistorial('Actualizó la deuda del medidor #' . $deuda->idMedidor, $detalle);
        return response()->json(['ok' => true, 'message' => 'Deuda actualizada correctamente'], 201);
    }

}
