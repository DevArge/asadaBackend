<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class Factura extends Model implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents{

//// FACTURAS DE CUENTAS POR COBRAR (no son los recibos de los medidores) /////

    protected $table = 'facturas';
    protected $fillable = ['idCuenta', 'numero', 'descripcion', 'fecha', 'sub_total', 'descuento', 'grand_total'];

    public function productos(){
        return $this->hasMany(DetalleFactura::class);
    }

    public function collection(){
      $sql = Factura::consultaReporte()
                  ->where('idCuenta', $_SESSION["id"])
                  ->whereBetween('facturas.fecha', [$_SESSION["fechaInicio"], $_SESSION["fechaFin"]])
                  ->get();
      unset($_SESSION["periodo"]);
      session_destroy();
      return $sql;
    }

    public function headings(): array{
         return [
           'Cuenta','Código',  'Número de factura',
           'Descripcion', 'Fecha','Total'
         ];
    }
    public function registerEvents(): array{
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:F1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
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
            ->join('cuentas', 'cuentas.id','=', 'facturas.idCuenta')
            ->select('cuentas.nombre as cuenta','cuentas.codigo', 'facturas.numero', 'cuentas.id as idCuenta', 'facturas.id',
                    'facturas.descripcion', 'facturas.created_at', 'facturas.fecha',
                    'facturas.grand_total');

    }

    public static function consultaReporte(){
        return DB::table('facturas')
            ->join('cuentas', 'cuentas.id','=', 'facturas.idCuenta')
            ->select('cuentas.nombre as cuenta','cuentas.codigo', 'facturas.numero',
                    'facturas.descripcion', 'facturas.fecha',
                    'facturas.grand_total');

    }

    public static function toString($old, $new, $eliminado = false){
      $detalle = 'Número: ' . $old->numero . "\r\n" .
            'Descripción: ' . $old->descripcion . "\r\n" .
            'Fecha: ' . $old->fecha . "\r\n" .
            'Sub Total: ' . $old->sub_total . "\r\n" .
            'Descuento: ' . $old->descuento . "\r\n" .
            'Total general: ' . $old->grand_total  . "\r\n";
      if ($eliminado) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Número: ' . $new->numero . "\r\n" .
        'Descripción: ' . $new->descripcion . "\r\n" .
        'Fecha: ' . $new->fecha . "\r\n" .
        'Sub Total: ' . $new->sub_total . "\r\n" .
        'Descuento: ' . $new->descuento . "\r\n" .
        'Total general: ' . $new->grand_total;
      }
    }
}
