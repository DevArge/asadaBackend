<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model{

    protected $table = 'detalle_facturas';
    protected $fillable = ['facturas_id', 'nombre', 'precio', 'cantidad', 'total'];

    public function factura(){
        return $this->belongsTo(Factura::class);
    }
}
