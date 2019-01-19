<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConfiguracionDeMedidor;

class ConfMedidorController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function getConfMedidores(){
        $configuracion = ConfiguracionDeMedidor::obtenerConfMedidores();
        return response()->json(['ok' => true, 'configuracion' => $configuracion], 200);
    }

    public function putConfMedidores(Request $request, $id){
        $valido = ConfiguracionDeMedidor::validarValorMetros($request);
        if ($valido != 'ok') {
            return response()->json(['ok'=> false, 'message' => $valido], 400);                    
        }
        $configuracion = ConfiguracionDeMedidor::find($id);
        if (!$configuracion) {
            return response()->json(['ok'=> false, 'message' => 'No existe ninguna configuración con el ID: ' . $id . ' no existe'], 403);        
        }
        $configuracion->fill($request->all());
        $configuracion->save();
        return response()->json(['ok' => true, 'message' => 'Configuración de medidores actualizada correctamente'], 201);
    }
    
}
