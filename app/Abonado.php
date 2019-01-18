<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Abonado extends Model{
    
    use SoftDeletes;
    protected $table = 'abonados';
    protected $fillable = ['nombre', 'cedula', 'apellido1', 'apellido2', 'telefono', 'email', 'direccion', 'plano'];
    protected $dates = ['deleted_at'];

    public static function obtenerAbonados($desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return DB::table('abonados')->where('deleted_at', null)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function buscarAbonado($termino){
        $nombre = DB::table('abonados')->where('nombre', 'like', "%{$termino}%")->where('deleted_at', null);
        $cedula = DB::table('abonados')->where('cedula', 'like', "%{$termino}%")->where('deleted_at', null);
        $apellido1 = DB::table('abonados')->where('apellido1', 'like', "%{$termino}%")->where('deleted_at', null);
        $apellido2 = DB::table('abonados')->where('apellido2', 'like', "%{$termino}%")->where('deleted_at', null);
        $email = DB::table('abonados')->where('email', 'like', "%{$termino}%")->where('deleted_at', null);
        return $telefono = DB::table('abonados')->where('telefono', 'like', "%{$termino}%")->where('deleted_at', null)
                ->union($nombre)
                ->union($cedula)
                ->union($apellido1)
                ->union($apellido2)
                ->union($email);
    }

    public static function paginarAbonados($termino, $desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return Abonado::buscarAbonado($termino)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function obtenerAbonado($idMedidor){
        return DB::table('abonados')
                ->join('medidores', 'abonados.id', '=', 'medidores.idAbonado')
                ->where('medidores.id', $idMedidor)
                ->value('abonados.id');
    }


}
