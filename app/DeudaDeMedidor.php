<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DeudaDeMedidor extends Model{

    protected $table = 'deuda_de_medidores';
    protected $fillable = ['idMedidor', 'costoTotal',  'deuda', 'plazo', 'tipoDeuda', 'estado', 'detalleDeuda'];

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

    public static function getDeudas($id){
        return DB::table('deuda_de_medidores')
        ->where('idMedidor', $id);
    }

    public static function paginar($desde, $cantidad, $columna, $orden, $id){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return DeudaDeMedidor::getDeudas($id)
                    ->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden);
    }

    public static function validarMultiplo($deuda, $deudaReq){
        if ($deudaReq === 0) {
            return true;
        }
        $abono = $deuda->costoTotal / $deuda->plazo;
        $suma = 0;
        while ($suma <= $deuda->costoTotal) {
            if ($suma == $deudaReq) {
                return true;
            }
            $suma = $suma + $abono;
        }
        return false;
    }

}
