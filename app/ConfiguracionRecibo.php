<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ConfiguracionRecibo extends Model{

    protected $table = 'configuracion_recibos';
    protected $fillable = ['notificacion', 'notificacionDefault', 'fechaInicio', 'fechaFin', 'impuestoRetraso'];

    public static function obtenerConfiguraciones(){
        return DB::table('configuracion_recibos')
                ->where('id', '>=', 1)
                ->value('id');
    }

    public static function toString($old, $new, $eliminado = false){
      $detalle =
            'Notificación: ' . $old->notificacion . "\r\n" .
            'Notificación por defecto: ' . $old->notificacionDefault . "\r\n" .
            'Fecha Inicio: ' . $old->fechaInicio . "\r\n" .
            'Fecha Fin: ' . $old->fechaFin . "\r\n" .
            'Impuesto por retraso de pago: ' . $old->impuestoRetraso . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Notificación: ' . $new->notificacion . "\r\n" .
        'Notificación por defecto: ' . $new->notificacionDefault . "\r\n" .
        'Fecha Inicio: ' . $new->fechaInicio . "\r\n" .
        'Fecha Fin: ' . $new->fechaFin . "\r\n" .
        'Impuesto por retraso de pago: ' . $new->impuestoRetraso . "\r\n";
      }
    }

}
