<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConfiguracionRecibo;
use App\Historial;
use DB;

class ConfReciboController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('secretaria');
    }

    public function getConfRecibos(){
        $configuracion = DB::table('configuracion_recibos')
                            ->where('id', '>=', 1)
                            ->first();
        return response()->json(['ok' => true, 'configuracion' => $configuracion], 200);
    }

    public function putConfRecibos(Request $request, $id){
        $configuracion = ConfiguracionRecibo::find($id);
        $original = ConfiguracionRecibo::find($id);
        if (!$configuracion) {
            return response()->json(['ok'=> false, 'message' => 'No existe ninguna configuración con el ID: ' . $id . ' no existe'], 403);
        }
        $configuracion->fill($request->all());
        $configuracion->save();
        $detalle = ConfiguracionRecibo::toString($original, $configuracion);
        Historial::crearHistorial('Actualizó las notificaciones o el impuesto de retraso ', $detalle);
        return response()->json(['ok' => true, 'configuracion' => $configuracion], 201);
    }

}
