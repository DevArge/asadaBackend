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

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'Nombre: ' . $old->nombre . "\r\n" .
            'Cédula: ' . $old->cedula . "\r\n" .
            'Primer Apellido: ' . $old->apellido1 . "\r\n" .
            'Segundo Apellido: ' . $old->apellido2 . "\r\n" .
            'Teléfono: ' . $old->telefono . "\r\n" .
            'Email: ' . $old->email . "\r\n" .
            'Dirección: ' . $old->direccion . "\r\n" .
            'Plano: ' . $old->plano . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Nombre: ' . $new->nombre . "\r\n" .
        'Cédula: ' . $new->cedula . "\r\n" .
        'Primer Apellido: ' . $new->apellido1 . "\r\n" .
        'Segundo Apellido: ' . $new->apellido2 . "\r\n" .
        'Teléfono: ' . $new->telefono . "\r\n" .
        'Email: ' . $new->email . "\r\n" .
        'Dirección: ' . $new->direccion . "\r\n" .
        'Plano: ' . $new->plano;
      }
    }


}
