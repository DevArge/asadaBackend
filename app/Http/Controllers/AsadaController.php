<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Asada;
use DB;

class AsadaController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('admin');
    }

    public function getAsada(){
        $asada = DB::table('asadas')->where('id', '>=', 1)->first();
        return response()->json(['ok' => true, 'asada' => $asada], 200);
    }

    public function putAsada(Request $request, $id){
        $asada = Asada::find($id);
        if (!$asada) {
            return response()->json(['ok'=> false, 'message' => 'LA ASADA con el ID: ' . $id . ' no existe'], 403);        
        }
        $asada->fill($request->all());
        $asada->save();
        return response()->json(['ok' => true, 'message' => 'ASADA actualizada correctamente'], 201);
    }
    
}
