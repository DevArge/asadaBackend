<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller{

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function getIndex(Request $r){
        $fecha = Carbon::now();
        $peri = $fecha->year . '-' . $fecha->month;
        $periodos = DB::table('lecturas')
                     ->select(DB::raw('sum(metros) as metros, periodo'))
                     ->where('periodo', 'like', "{$fecha->year}%")
                     ->groupBy('periodo')
                     ->orderBy('periodo', 'asc')
                     ->get();
        $recibos = DB::table('recibos')->where('estado', 'PENDIENTE')->count();
        $abonados = DB::table('abonados')->where('deleted_at', null)->count();
        $medidores = DB::table('medidores')->where('estado', 'ACTIVO')->count();
        $ganancias = DB::table('recibos')->where('periodo',$peri)->where('estado', 'PAGADO')->sum('total');
        return response()->json(['ok'=> true, 'periodos' => $periodos,
                                              'recibos' => $recibos,
                                              'abonados' => $abonados,
                                              'medidores' => $medidores,
                                              'ganancias' => $ganancias], 200);
    }

}
