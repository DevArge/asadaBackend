<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Historial extends Model{
    protected $table = 'historiales';
    protected $fillable = ['idUsuario', 'actividad',  'detalle'];

    public static function paginar($desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return DB::table('historiales')
                    ->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden);
    }
}
