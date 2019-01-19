<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medidor;
use App\DeudaDeMedidor;
use DB;

class DeudaController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function getDeudas(Request $r, $id){
        $medidor = Medidor::find($id);
        if (!$medidor) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);                                            
        }
        $deudas = DeudaDeMedidor::paginar($r->desde, $r->cantidad, $r->columna, $r->orden, $id)->get();
        $total = DeudaDeMedidor::paginar($r->desde, $r->cantidad, $r->columna, $r->orden, $id)->count();
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
        DeudaDeMedidor::guardar($request, $medidor->id);
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
        DeudaDeMedidor::actualizar($deuda->idMedidor, $request->deuda, $deuda->tipoDeuda);
        return response()->json(['ok' => true, 'message' => 'Deuda actualizada correctamente'], 201);        
    }

}