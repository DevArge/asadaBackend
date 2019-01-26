<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Factura extends Model{
    
//// FACTURAS DE CUENTAS POR COBRAR (no son los recibos de los medidores) /////

    protected $table = 'facturas';
    protected $fillable = ['idCuenta', 'numero', 'descripcion', 'fecha', 'sub_total', 'descuento', 'grand_total'];

    public function productos(){
        return $this->hasMany(DetalleFactura::class); 
    }

    public static function obtenerFacturas($desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return  Factura::consultaSQL()
                    ->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden)
                    ->get();
    }

    public static function buscarFactura($termino){
        $nombre = Factura::consultaSQL()->where('cuentas.nombre', 'like', "%{$termino}%");
        $descripcion = Factura::consultaSQL()->where('facturas.descripcion', 'like', "%{$termino}%");
        return Factura::consultaSQL()->where('facturas.numero', 'like', "%{$termino}%")
                ->union($descripcion)
                ->union($nombre);
    }

    public static function paginarFacturas($termino, $desde, $cantidad, $columna, $orden){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return Factura::buscarFactura($termino)
                ->skip($desde)
                ->take($cantidad)
                ->orderBy($columna, $orden)
                ->get();
    }

    public static function consultaSQL(){
        return DB::table('facturas')
            ->join('cuentas', 'cuentas.id','=', 'facturas.idCuentaPorPagar')
            ->select('cuentas.nombre as cuenta', 'cuentas.id as idCuenta', 'facturas.id',
                    'facturas.descripcion', 'facturas.fecha', 'facturas.numero',
                    'facturas.grand_total', 'facturas.created_at');
            
  }
}
