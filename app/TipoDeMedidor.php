<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class TipoDeMedidor extends Model{
    
    protected $table = 'tipo_de_medidores';
    protected $fillable = ['nombre', 'precio', 'personalizado'];

    public static function obtenerCargoFijo($idMedidor){
        return DB::table('medidores')
                ->join('tipo_de_medidores', 'tipo_de_medidores.id', '=', 'medidores.idTipoDeMedidor')
                ->where('tipo_de_medidores.id', '>=', 1)
                ->first();
    }

}
