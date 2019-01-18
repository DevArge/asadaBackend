<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DeudaRecibo extends Model{

    protected $table = 'deuda_recibos';
    protected $fillable = ['idRecibo', 'idDeuda'];
     
    public static function guardar($recibo, $deudaReparacion, $deudaAbono, $deudaReactivacion){
        if ($deudaReparacion) {
            DeudaRecibo::validar($recibo->reparacion, $recibo->id, $deudaReparacion->id);
        }
        if ($deudaAbono) {
            DeudaRecibo::validar($recibo->abonoMedidor, $recibo->id, $deudaAbono->id);
        }
        if ($deudaReactivacion) {
            DeudaRecibo::validar($recibo->reactivacionMedidor, $recibo->id, $deudaReactivacion->id);
        }
    }

    public static function validar($tipoDeuda, $idRecibo, $idDeuda){
        if($tipoDeuda > 0 ){
            $deuda = DB::table('deuda_recibos')
                        ->where('idRecibo', $idRecibo)
                        ->where('idDeuda', $idDeuda)->first();
            if (!$deuda) {
                $deudaRecibo = new DeudaRecibo();
                $deudaRecibo->idRecibo = $idRecibo;
                $deudaRecibo->idDeuda = $idDeuda;
                $deudaRecibo->save();
            } 
        }
    }

    public static function getDeudaRecibo($idRecibo){
        return DB::table('deuda_recibos')
            ->where('idRecibo', $idRecibo)
            ->get();
    }

}
