<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ConfiguracionDeMedidor;
use App\ConfiguracionRecibo;
use App\DeudaDeMedidor;
use App\DeudaRecibo;
use App\TipoDeMedidor;
use App\Abonado;
use App\Asada;
use Carbon\Carbon;
use DB;

class Recibo extends Model{

    protected $table = 'recibos';
    protected $fillable = ['idMedidor', 'idAbonado', 'idLectura', 'idAsada',  'idConfiguracionRecibos', 
                           'periodo', 'estado', 'reparacion', 'abonoMedidor', 'reactivacionMedidor',
                           'retrasoPago', 'metrosConsumidos', 'cargoFijo', 'total', 'hidrante', 'valorMetro', 'vence'];

    public static function crearRecibo($idMedidor, $idLectura, $periodo, $metros){
        $vence = Carbon::createFromFormat('Y-m-d', $periodo . '-01')->addMonth()->addDays(14);
        $existe = DB::table('recibos')->where('idMedidor', $idMedidor)->where('periodo', $periodo)->value('id');
        $confMedidor = ConfiguracionDeMedidor::obtenerConfMedidores();
        $cargo = TipoDeMedidor::obtenerCargoFijo($idMedidor);
        $deudaReparacion = DeudaDeMedidor::getDeuda($idMedidor, 'REPARACION');
        $deudaAbono = DeudaDeMedidor::getDeuda($idMedidor, 'ABONO');
        $deudaReactivacion = DeudaDeMedidor::getDeuda($idMedidor, 'REACTIVACION');
        $recibo = null;
        if ($existe) {
            $recibo = Recibo::find($existe);
        }else {
            $recibo = new Recibo();
            $recibo->idMedidor = $idMedidor;
            $recibo->idAbonado = Abonado::obtenerAbonado($idMedidor);
            $recibo->idLectura = $idLectura;
            $recibo->idAsada = Asada::obtenerAsada();
            $recibo->idConfiguracionRecibos = ConfiguracionRecibo::obtenerConfiguraciones();
            $recibo->periodo = $periodo;
            $recibo->estado = 'PENDIENTE';
            $recibo->reparacion = $recibo->obtenerDeuda($idMedidor, $deudaReparacion, 'REPARACION');
            $recibo->abonoMedidor = $recibo->obtenerDeuda($idMedidor, $deudaAbono, 'ABONO');
            $recibo->reactivacionMedidor = $recibo->obtenerDeuda($idMedidor, $deudaReactivacion, 'REACTIVACION');
            $recibo->retrasoPago = 0;
            $recibo->cargoFijo = $cargo->precio;
        }
        $recibo->metrosConsumidos = $metros;
        $recibo->hidrante = $confMedidor->impuestoHidrante * $metros;
        $recibo->valorMetro = $recibo->calcularValorMetro($metros, $confMedidor);
        $recibo->total = $recibo->calcularTotal($recibo, $cargo);
        $recibo->vence = $vence;
        $recibo->save();
        DeudaRecibo::guardar($recibo, $deudaReparacion, $deudaAbono, $deudaReactivacion);
    }

    public function calcularTotal($recibo, $cargo){
        if ($cargo->personalizado) {
            return $cargo->precio;
        }else {
            $consumo = $recibo->metrosConsumidos * $recibo->valorMetro;
            return $consumo + $recibo->reparacion + $recibo->abonoMedidor + $recibo->hidrante +
                   $recibo->reactivacionMedidor + $recibo->retrasoPago + $recibo->cargoFijo;
        }
    }

    public function obtenerDeuda($idMedidor, $deuda, $tipo){
        if ($deuda) {
            $pago = $deuda->costoTotal / $deuda->plazo;
            $deuda = $deuda->deuda - $pago;
            DeudaDeMedidor::actualizar($idMedidor, $deuda, $tipo);
            return $pago;
        }else {
            return 0;
        }
    }

    public function calcularValorMetro($metros, $confMedidor){
        if ($metros <= 10 ) {
            return $confMedidor->unoAdiez;
        }elseif($metros >= 11 && $metros <=30){
            return $confMedidor->onceAtreinta;
        }elseif($metros >= 31 && $metros <= 60){
            return $confMedidor->treintaYunoAsecenta;
        }if ($metros > 60) {
            return $confMedidor->masDeSecenta;
        }
    }

    public function devolverDeudas($recibo){
        return DB::transaction(function ()use($recibo) {
            $deudas = DeudaRecibo::getDeudaRecibo($recibo->id);
            $reparacion = 0;
            $abonoMedidor = 0;
            $reactivacionMedidor = 0;
            foreach ($deudas as $deuda) {
                $tipo = DB::table('deuda_de_medidores')
                    ->where('idMedidor', $recibo->idMedidor)
                    ->where('id', $deuda->idDeuda)->first();
                if ($tipo) {
                    if ($tipo->tipoDeuda == 'REPARACION') {
                        $reparacion = $tipo;
                    }
                    if ($tipo->tipoDeuda == 'ABONO') {
                        $abonoMedidor = $tipo;
                    }
                    if ($tipo->tipoDeuda == 'REACTIVACION') {
                        $reactivacionMedidor = $tipo;
                    }
                }
            }
            if ($recibo->reparacion > 0 ) {     
                Recibo::reintegrarDeuda($reparacion, 'REPARACION', $recibo->idMedidor);
            } 
            if($recibo->abonoMedidor > 0 ){
                Recibo::reintegrarDeuda($abonoMedidor, 'ABONO', $recibo->idMedidor);            
            } 
            if($recibo->reactivacionMedidor > 0 ){
                Recibo::reintegrarDeuda($reactivacionMedidor, 'REACTIVACION', $recibo->idMedidor);
            }
            DB::table('deuda_recibos')->where('idRecibo', $recibo->id)->delete();
            return true;
        });
    }

    public static function reintegrarDeuda($deuda, $tipo, $idMedidor){
        $pago = $deuda->costoTotal / $deuda->plazo;
        $reintegrar = $deuda->deuda + $pago;
        DeudaDeMedidor::actualizar($idMedidor, $reintegrar, $tipo);
    }

    public static function obtenerRecibos($desde, $cantidad, $columna, $orden, $periodo){
        $query = Recibo::consultaSQL()->where('recibos.periodo', $periodo);
        return Recibo::paginar($desde, $cantidad, $columna, $orden, $query)->get();
    }

    public static function obtenerRecibosUnMedidor($desde, $cantidad, $columna, $orden, $id){
        $query = Recibo::consultaSQL()->where('recibos.idMedidor', $id);
        return Recibo::paginar($desde, $cantidad, $columna, $orden, $query)->get();
    }

    public static function paginar($desde, $cantidad, $columna, $orden, $query){
        $desde = $desde ? $desde : 0;
        $cantidad = $cantidad ? $cantidad : 10;
        $columna = $columna ? $columna : 'id';
        $orden = $orden ? $orden : 'asc';
        return $query->skip($desde)
                    ->take($cantidad)
                    ->orderBy($columna, $orden);
    }

    public static function buscarRecibos($termino, $periodo, $desde, $cantidad, $columna, $orden){
        $nombre = Recibo::consultaSQL()->where('recibos.periodo', $periodo)->where('abonados.nombre', 'like', "%{$termino}%");
        $cedula = Recibo::consultaSQL()->where('recibos.periodo', $periodo)->where('cedula', 'like', "%{$termino}%");
        $apellido1 = Recibo::consultaSQL()->where('recibos.periodo', $periodo)->where('apellido1', 'like', "%{$termino}%");
        $query = Recibo::consultaSQL()->where('recibos.periodo', $periodo)->where('apellido2', 'like', "%{$termino}%")
                ->union($nombre)
                ->union($cedula)
                ->union($apellido1);
        return Recibo::paginar($desde, $cantidad, $columna, $orden, $query);
    }

    public static function buscarRecibosUnmedidor($termino, $idMedidor){
        return Recibo::consultaSQL()->where('recibos.idMedidor', $idMedidor)->where('recibos.periodo', 'like', "%{$termino}%");
    }

    public static function calcularCuentas($periodo){
        return DB::table('recibos')
                ->select(DB::raw('sum(abonoMedidor) as abonoMedidor, sum(reparacion) as reparacion,
                sum(reactivacionMedidor) as reactivacionMedidor, sum(retrasoPago) as retrasoPago, sum(cargoFijo) as cargoFijo,
                sum(hidrante) as hidrante, sum(metrosConsumidos * valorMetro) as consumo, sum(total) as total'))
                ->where('periodo', $periodo)
                ->get();
        
    }

    public static function consultaSQL(){
        return $recibos = DB::table('abonados')
        ->join('medidores', 'medidores.idAbonado', '=', 'abonados.id')
        ->join('tipo_de_medidores', 'tipo_de_medidores.id', '=', 'medidores.idTipoDeMedidor')
        ->join('lecturas', 'medidores.id', '=', 'lecturas.idMedidor')
        ->join('recibos', 'lecturas.id', '=', 'recibos.idLectura')
        ->join('configuracion_recibos', 'configuracion_recibos.id', '=', 'recibos.idConfiguracionRecibos')
        ->select('abonados.id as abonado', 'abonados.cedula', 'abonados.nombre', 'abonados.apellido1', 'abonados.apellido2',
                'abonados.direccion', 'medidores.id as medidor', 'tipo_de_medidores.nombre as tipo', 'lecturas.lectura',
                'lecturas.metros', 'recibos.valorMetro', 'recibos.id', 'recibos.periodo', 'recibos.vence', 'recibos.cargoFijo',
                'recibos.estado', 'reparacion', 'abonoMedidor', 'reactivacionMedidor', 'retrasoPago','total', 'hidrante');
        
    }

}
