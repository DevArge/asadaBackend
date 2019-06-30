<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use JWTAuth;

class Historial extends Model{
    protected $table = 'historiales';
    protected $fillable = ['idUsuario', 'actividad',  'detalle'];

    public static function paginar($desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'historiales.created_at';
        $orden = $orden ? $orden : 'desc';
        return DB::table('historiales')
                    ->join('users', 'users.id', '=', 'historiales.idUsuario')
                    ->select('users.id','users.nombre', 'actividad',  'detalle', 'historiales.created_at')
                    ->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden);
    }

    public static function crearHistorial($actividad, $detalle){
      $token = JWTAuth::getToken();
      $newToken = JWTAuth::getPayload($token)->toArray();
      $historial = new Historial();
      $historial->fill([
        'idUsuario' => $newToken['sub'],
        'actividad' => $actividad,
        'detalle'   => $detalle
      ]);
      $historial->save();
    }
}
