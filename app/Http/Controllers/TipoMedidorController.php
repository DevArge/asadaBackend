<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TipoMedidorRequest;
use App\TipoDeMedidor;


class TipoMedidorController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }
    
    public function getTipodeMedidores(){
        $tipos = TipoDeMedidor::all();
        return response()->json(['ok'=> true, 'tiposDeMedidores' => $tipos], 200);
    }

    public function postTipoMedidor(TipoMedidorRequest $request){
        $tipo = new TipoDeMedidor();
        $tipo->fill($request->all());
        $tipo->save();
        return response()->json(['ok' => true, 'abonado' => $tipo], 201);
    }
}
