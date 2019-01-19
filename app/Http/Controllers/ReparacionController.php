<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medidor;
use App\DeudaDeMedidor;
use DB;

class ReparacionController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
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


}