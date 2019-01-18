<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ConfiguracionDeMedidor extends Model{
    
    protected $table = 'configuracion_de_medidores';
    protected $fillable = ['impuestoHidrante', 'unoAdiez', 'onceAtreinta', 'treintaYunoAsecenta', 
                            'masDeSecenta', 'impuestoReactivacion', 'impuestoRetraso'];

    public static function obtenerConfMedidores(){
        return DB::table('configuracion_de_medidores')
        ->where('id', '>=', 1)
        ->first();
    }
}
