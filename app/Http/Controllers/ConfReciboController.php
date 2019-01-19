<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConfiguracionRecibo;
use DB;

class ConfReciboController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function getConfRecibos(){
        $configuracion = DB::table('configuracion_recibos')
                            ->where('id', '>=', 1)
                            ->first();
        return response()->json(['ok' => true, 'configuracion' => $configuracion], 200);
    }

    public function putConfRecibos(Request $request, $id){
        $configuracion = ConfiguracionRecibo::find($id);
        if (!$configuracion) {
            return response()->json(['ok'=> false, 'message' => 'No existe ninguna configuración con el ID: ' . $id . ' no existe'], 403);        
        }
        $configuracion->fill($request->all());
        $configuracion->save();
        return response()->json(['ok' => true, 'message' => 'Configuración de recibos actualizada correctamente'], 201);
    }
    
}
