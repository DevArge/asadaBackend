<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class TipoDeMedidor extends Model{

    use SoftDeletes;
    protected $table = 'tipo_de_medidores';
    protected $fillable = ['nombre', 'precio', 'personalizado'];
    protected $dates = ['deleted_at'];

    public static function obtenerCargoFijo($idMedidor){
        return DB::table('medidores')
                ->join('tipo_de_medidores', 'tipo_de_medidores.id', '=', 'medidores.idTipoDeMedidor')
                ->where('medidores.id', '=', $idMedidor)
                ->first();
    }

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'Nombre: ' . $old->nombre . "\r\n" .
            'Precio: ' . $old->precio . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Nombre: ' . $new->nombre . "\r\n" .
        'Precio: ' . $new->precio . "\r\n";
      }
    }

}
