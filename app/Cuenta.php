<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
USE DB;

class Cuenta extends Model{

    use SoftDeletes;
    protected $table = 'cuentas';
    protected $fillable = ['idAsada','codigo', 'nombre', 'tipo', 'description', 'presupuesto'];
    protected $dates = ['deleted_at'];

    public static function obtenerCuentas($desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return DB::table('cuentas')->where('deleted_at', null)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function buscarCuenta($termino){
        $nombre = DB::table('cuentas')->where('nombre', 'like', "%{$termino}%")->where('deleted_at', null);
        return DB::table('cuentas')->where('tipo', 'like', "%{$termino}%")->where('deleted_at', null)
                ->union($nombre);
    }

    public static function paginarCuentas($termino, $desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return Cuenta::buscarCuenta($termino)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'idAsada: ' . $old->idAsada . "\r\n" .
            'Código: ' . $old->codigo . "\r\n" .
            'Nombre: ' . $old->nombre . "\r\n" .
            'Tipo: ' . $old->tipo . "\r\n" .
            'Descripción: ' . $old->descripcion . "\r\n" .
            'Presupuesto: ' . $old->presupuesto . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'idAsada: ' . $new->nombre . "\r\n" .
        'Código: ' . $new->codigo . "\r\n" .
        'Nombre: ' . $new->nombre . "\r\n" .
        'Tipo: ' . $new->tipo . "\r\n" .
        'Descripción: ' . $new->descripcion . "\r\n" .
        'Presupuesto: ' . $new->presupuesto . "\r\n";
      }
    }

}
