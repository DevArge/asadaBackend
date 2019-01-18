<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DeudaDeMedidor extends Model{

    protected $table = 'deuda_de_medidores';
    protected $fillable = ['idMedidor', 'costoTotal',  'deuda', 'plazo', 'tipoDeuda', 'estado'];

    public static function guardar($req, $idMedidor){
        $deuda = new DeudaDeMedidor();
        $deuda->fill($req->all());
        $deuda->estado = 'PENDIENTE';
        $deuda->deuda = $req->costoTotal;
        $deuda->idMedidor = $idMedidor;
        $deuda->save();
    }

    public static function actualizar($idMedidor, $monto, $tipoDeuda){
        $arreglo = $monto == 0 ? ['deuda' => $monto, 'estado' => 'COBRADO'] : ['deuda' => $monto, 'estado' => 'PENDIENTE'];
        $deuda = DB::table('deuda_de_medidores')
            ->where('idMedidor', $idMedidor)
            ->where('tipoDeuda', $tipoDeuda)
            ->update($arreglo);
    }

    public static function getDeuda($idMedidor, $tipoDeuda){
        return DB::table('deuda_de_medidores')
        ->where('idMedidor', $idMedidor)
        ->where('tipoDeuda', $tipoDeuda)
        ->where('estado', 'PENDIENTE')
        ->first();
    }
    
}
