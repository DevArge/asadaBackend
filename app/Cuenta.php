<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
USE DB;

class Cuenta extends Model{

    use SoftDeletes;
    protected $table = 'cuentas';
    protected $fillable = ['idAsada', 'nombre', 'tipo', 'presupuesto'];
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
    
}
