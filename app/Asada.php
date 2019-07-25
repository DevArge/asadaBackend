<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Asada extends Model{

    protected $table = 'asadas';
    protected $fillable = ['nombre', 'cedulaJuridica',  'telefono', 'direccion', 'correo'];

    public static function obtenerAsada(){
        return DB::table('asadas')
                ->where('id', '>=', 1)
                ->value('id');
    }

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'Nombre: ' . $old->nombre . "\r\n" .
            'Cédula Jurídica: ' . $old->cedulaJuridica . "\r\n" .
            'Teléfono: ' . $old->telefono . "\r\n" .
            'Correo: ' . $old->correo . "\r\n" .
            'Dirección: ' . $old->direccion . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Nombre: ' . $new->nombre . "\r\n" .
        'Cédula Jurídica: ' . $new->cedulaJuridica . "\r\n" .
        'Teléfono: ' . $new->telefono . "\r\n" .
        'Correo: ' . $new->correo . "\r\n" .
        'Dirección: ' . $new->direccion . "\r\n";
      }
    }

}
