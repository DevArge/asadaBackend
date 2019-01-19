<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Authenticatable implements JWTSubject{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'email', 'password', 'idAsada', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $dates = ['deleted_at'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }

    public static function obtenerUsers($desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return DB::table('users')->select('id','nombre', 'email', 'role', 'created_at')->where('deleted_at', null)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function buscarUsuario($termino){
        $nombre = DB::table('users')
                ->select('id','nombre', 'email', 'role', 'created_at')
                ->where('nombre', 'like', "%{$termino}%")
                ->where('deleted_at', null);
        return DB::table('users')
                ->select('id','nombre', 'email', 'role', 'created_at')
                ->where('email', 'like', "%{$termino}%")
                ->where('deleted_at', null)
                ->union($nombre);
    }

    public static function paginarUsuarios($termino, $desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return User::buscarUsuario($termino)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

}
