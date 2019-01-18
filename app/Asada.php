<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Asada extends Model{

    protected $table = 'asadas';
    protected $fillable = ['nombre', 'cedulaJuridica',  'telefono', 'direccion'];

    public static function obtenerAsada(){
        return DB::table('asadas')
                ->where('id', '>=', 1)
                ->value('id');
    }
    
}
