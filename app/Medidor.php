<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Medidor extends Model{

    protected $table = 'medidores';
    protected $fillable = ['idAbonado', 'idTipoDeMedidor',  'estado', 'detalle'];

    public static function obtenerMedidores($desde, $cantidad, $columna, $orden, $todos){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return  Medidor::consultaSQL($todos)
                    ->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden)
                    ->get();
    }

    public static function buscarMedidor($termino, $todos){
        $nombre = Medidor::consultaSQL($todos)->where('abonados.nombre', 'like', "%{$termino}%");
        $cedula = Medidor::consultaSQL($todos)->where('cedula', 'like', "%{$termino}%");
        $apellido1 = Medidor::consultaSQL($todos)->where('apellido1', 'like', "%{$termino}%");
        $detalle = Medidor::consultaSQL($todos)->where('detalle', 'like', "%{$termino}%");
        return $apellido2 = Medidor::consultaSQL($todos)->where('apellido2', 'like', "%{$termino}%")
                ->union($nombre)
                ->union($cedula)
                ->union($apellido1)
                ->union($detalle);
    }

    public static function paginarMedidores($termino, $desde, $cantidad, $columna, $orden, $todos){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return Medidor::buscarMedidor($termino, $todos)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function consultaSQL($todos){
        $query = DB::table('abonados')
        ->join('medidores', 'abonados.id', '=', 'medidores.idAbonado')
        ->join('tipo_de_medidores', 'tipo_de_medidores.id', '=', 'medidores.idTipoDeMedidor')
        ->select('abonados.id', 'abonados.cedula', 'abonados.nombre', 'abonados.apellido1', 'abonados.apellido2',
         'medidores.id as medidor', 'medidores.estado', 'medidores.idTipoDeMedidor', 'medidores.detalle','tipo_de_medidores.nombre as tipo');
        if (!$todos) {
            return $query->where('estado', 'ACTIVO');
        }else{
            return $query;
        }
    }
}
