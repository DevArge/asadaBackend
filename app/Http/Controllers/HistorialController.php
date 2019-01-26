<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Historial;

class HistorialController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('admin');
    }

    public function getHistoriales(Request $r){
        $historiales = Historial::paginar($r->desde, $r->cantidad, $r->columna, $r->orden)->get();
        $total = Historial::paginar($r->desde, $r->cantidad, $r->columna, $r->orden)->count();
        return response()->json(['ok' => true, 'historiales' => $historiales, 'total' => $total], 200);
    }

    public function postHistorial(Request $r){
        $user = User::find($r->idUsuario);
        if (!$user) {
            return response()->json(['ok'=> false, 'message' => 'El usuario con el ID: ' . $id . ' no existe'], 403);        
        }
        $historial = new Historial();
        $historial->fill($r->all());
        $historial->save();
        return response()->json(['ok' => true, 'historial' => $historial], 201);
    }
    
}
