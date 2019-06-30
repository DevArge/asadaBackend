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
        return $deuda;
    }

    public static function actualizar($idMedidor, $monto, $tipoDeuda){
        $arreglo = $monto == 0 ? ['deuda' => $monto, 'estado' => 'COBRADO'] : ['deuda' => $monto, 'estado' => 'PENDIENTE'];
        DB::table('deuda_de_medidores')
            ->where('idMedidor', $idMedidor)
            ->where('tipoDeuda', $tipoDeuda)
            ->update($arreglo);
        return DB::table('deuda_de_medidores')
            ->where('idMedidor', $idMedidor)
            ->where('tipoDeuda', $tipoDeuda)
            ->first();
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

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'Medidor: #' . $old->idMedidor . "\r\n" .
            'Costo Total: ' . $old->costoTotal . "\r\n" .
            'Deuda: ' . $old->deuda . "\r\n" .
            'Plazo: ' . $old->plazo . "\r\n" .
            'Tipo de deuda: ' . $old->tipoDeuda . "\r\n" .
            'Estado: ' . $old->estado . "\r\n" .
            'Detalle: ' . $old->detalleDeuda . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Medidor: #: ' . $new->idMedidor . "\r\n" .
        'Costo Total: ' . $new->costoTotal . "\r\n" .
        'Deuda: ' . $new->deuda . "\r\n" .
        'Plazo: ' . $new->plazo . "\r\n" .
        'Tipo de deuda: ' . $new->tipoDeuda . "\r\n" .
        'Estado: ' . $new->estado . "\r\n" .
        'Detalle: ' . $new->detalleDeuda . "\r\n";
      }
    }

}
