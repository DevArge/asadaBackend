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
}
