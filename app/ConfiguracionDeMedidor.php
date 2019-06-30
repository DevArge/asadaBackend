<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ConfiguracionDeMedidor extends Model{

    protected $table = 'configuracion_de_medidores';
    protected $fillable = ['impuestoHidrante', 'unoAdiez', 'onceAtreinta', 'treintaYunoAsecenta',
                            'masDeSecenta', 'impuestoReactivacion'];

    public static function obtenerConfMedidores(){
        return DB::table('configuracion_de_medidores')
        ->where('id', '>=', 1)
        ->first();
    }

    public static function validarValorMetros($r){
        if ($r->unoAdiez > $r->onceAtreinta || $r->unoAdiez > $r->treintaYunoAsecenta || $r->unoAdiez > $r->masDeSecenta) {
            return 'el valor de metro "de uno a diez" no puede ser mayor que "de once a treinta", "de treinta y uno a secenta" o "más de secenta"';
        }elseif ($r->onceAtreinta > $r->treintaYunoAsecenta || $r->onceAtreinta > $r->masDeSecenta) {
            return 'el valor de metro "de once a treinta" no puede ser mayor que "de treinta y uno a secenta" o "más de secenta"';
        }elseif ($r->treintaYunoAsecenta > $r->masDeSecenta) {
            return 'el valor de metro "de treinta y uno a secenta" no puede ser mayor que "más de secenta"';
        }else {
            return 'ok';
        }
    }

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'Impuesto Hidrante: ' . $old->impuestoHidrante . "\r\n" .
            'Valor metro de 1 a 10: ' . $old->unoAdiez . "\r\n" .
            'Valor metro de 11 a 30: ' . $old->onceAtreinta . "\r\n" .
            'Valor metro de 31 a 60: ' . $old->treintaYunoAsecenta . "\r\n" .
            'Valor metro de más de 60: ' . $old->masDeSecenta . "\r\n" .
            'Impuesto de reactivación : ' . $old->impuestoReactivacion . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Impuesto Hidrante: ' . $new->impuestoHidrante . "\r\n" .
        'Valor metro de 1 a 10: ' . $new->unoAdiez . "\r\n" .
        'Valor metro de 11 a 30: ' . $new->onceAtreinta . "\r\n" .
        'Valor metro de 31 a 60: ' . $new->treintaYunoAsecenta . "\r\n" .
        'Valor metro de más de 60: ' . $new->masDeSecenta . "\r\n" .
        'Impuesto de reactivación : ' . $new->impuestoReactivacion . "\r\n";
      }
    }
}
