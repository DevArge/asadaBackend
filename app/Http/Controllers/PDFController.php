<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recibo;
use App\DeudaDeMedidor;
use App\ConfiguracionRecibo;
use App\Lectura;
use PDF;
use App;
use View;
use DB;

class PDFController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->middleware('secretaria');
    } 

    public function getRecibo(Request $r, $id){
        $recibo = Recibo::consultaSQL()->where('recibos.id', '=', $id)->first();
        if (!$recibo) {
            return response()->json(['ok'=> false, 'message' => 'El recibo con el ID: ' . $id . ' no existe'], 403);        
        };
        $asada = DB::table('asadas')->where('id', '>=', 1)->first();
        $deudas = DeudaDeMedidor::getDeudas($recibo->medidor)->where('estado', 'PENDIENTE')->get();
        $configuracion = DB::table('configuracion_recibos')->where('id', '>=', 1)->first();
        $lecturasAnteriores = Lectura::lecturasAnteriores($recibo->medidor, $recibo->periodo);
        $recibosPendientes = DB::table('recibos')
            ->where('idMedidor', $recibo->medidor)
            ->where('estado', 'PENDIENTE')
            ->where('periodo', '<', $recibo->periodo)
            ->get();
        $view = View::make('recibos.index', compact('recibo', 'asada', 'deudas', 'configuracion', 'lecturasAnteriores', 'recibosPendientes'))->render();
        $pdf = App::make('dompdf.wrapper');
        // $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        $pdf->loadHTML($view);
        // return $pdf->download('Recibo-' . $recibo->periodo .'.pdf');
        return $pdf->stream();
    }

}
