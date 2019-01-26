<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ConfiguracionRecibo extends Model{

    protected $table = 'configuracion_recibos';
    protected $fillable = ['nombre', 'notificacion', 'notificacionDefault', 'fechaInicio', 'fechaFin', 'impuestoRetraso'];

    public static function obtenerConfiguraciones(){
        return DB::table('configuracion_recibos')
                ->where('id', '>=', 1)
                ->value('id');
    }

}
