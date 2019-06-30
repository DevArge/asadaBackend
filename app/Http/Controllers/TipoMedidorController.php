<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TipoMedidorRequest;
use App\TipoDeMedidor;
use App\Historial;


class TipoMedidorController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('admin');
    }

    public function getTipodeMedidores(){
        $tipos = TipoDeMedidor::all();
        return response()->json(['ok'=> true, 'tiposDeMedidores' => $tipos], 200);
    }

    public function putTipoMedidor(Request $request, $id = null){
        $tipo = TipoDeMedidor::find($id);
        $original = TipoDeMedidor::find($id);
        if (!$tipo) {
            return response()->json(['ok'=> false, 'message' => 'El tipo de medidor con el ID: ' . $id . ' no existe'], 403);
        }
        $tipo->fill($request->all());
        $tipo->save();
        $detalle = TipoDeMedidor::toString($original, $tipo);
        Historial::crearHistorial('Actualizó el tipo de medidor: ' . $original->nombre, $detalle);
        return response()->json(['ok' => true, 'message' => 'Tipo de medidor actualizado correctamente'], 201);
    }

    public function postTipoMedidor(TipoMedidorRequest $request){
        $tipo = new TipoDeMedidor();
        $tipo->fill($request->all());
        $tipo->save();
        $detalle = TipoDeMedidor::toString($tipo, $tipo, true);
        Historial::crearHistorial('Creó el tipo de medidor: ' . $tipo->nombre, $detalle);
        return response()->json(['ok' => true, 'abonado' => $tipo], 201);
    }

    public function deleteTipoMedidor($id){
        $tipo = TipoDeMedidor::find($id);
        if (!$tipo) {
            return response()->json(['ok'=> false, 'message' => 'El Tipo de medidor con el ID: ' . $id . ' no existe'], 403);
        }
        $tipo->delete();
        $detalle = TipoDeMedidor::toString($tipo, $tipo, true);
        Historial::crearHistorial('Eliminó el tipo de medidor: ' . $tipo->nombre, $detalle);
        return response()->json(['ok' => true, 'message' => 'Tipo de Medidor eliminado correctamente'], 201);
    }


}
