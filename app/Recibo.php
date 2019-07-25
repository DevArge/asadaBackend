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
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class Recibo extends Model implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents{

    protected $table = 'recibos';
    protected $fillable = ['idMedidor', 'idAbonado', 'idLectura', 'idAsada',  'idConfiguracionRecibos',
                           'periodo', 'estado', 'reparacion', 'abonoMedidor', 'reactivacionMedidor',
                           'retrasoPago', 'metrosConsumidos', 'cargoFijo', 'total', 'hidrante', 'valorMetro', 'vence'];

    public function collection(){
      $sql = Recibo::consultaSQL()->where('recibos.periodo', $_SESSION["periodo"])->get();
      unset($_SESSION["periodo"]);
      session_destroy();
      return $sql;
    }

    public function headings(): array{
         return [
           'Abonado', 'Cédula', 'Nombre', 'Apellido1', 'Apellido2',
           'Dirección', 'Medidor', 'Detalle del medidor', 'Tipo', 'Personalizado', 'Lectura',
           'Metros', 'Nota', 'Valor del Metro', 'Numero de recibo', 'Periodo', 'Fecha de vencimiento', 'Cargo Fijo',
           'Estado', 'Costo de Reparación', 'Abono de Medidor', 'Reactivacion del Medidor', 'Retraso de Pago', 'Hidrante', 'Generado','Total'
         ];
    }
    public function registerEvents(): array{
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:Z1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

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

    public static function devolverDeudas($recibo){
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

    public static function obtenerRecibosUnAbonado($desde, $cantidad, $columna, $orden, $id){
        $query = Recibo::consultaSQL()->where('recibos.idAbonado', $id)->orderBy('recibos.periodo', 'ASC');
        return Recibo::paginar($desde, $cantidad, $columna, $orden, $query)->get();
    }

    public static function obtenerRecibosUnAbonadoPendiente($desde, $cantidad, $columna, $orden, $id){
        $query = Recibo::consultaSQL()->where('recibos.idAbonado', $id)->where('recibos.estado', 'PENDIENTE')->orderBy('recibos.periodo', 'ASC');
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

    public static function anadirImpuesto($periodo){
      $vence = DB::table('recibos')->where('periodo', $periodo)->first();
      if ($vence) {
        $fechaVence = Carbon::createFromFormat('Y-m-d', $vence->vence);
        $hoy = Carbon::now();
        if ($fechaVence < $hoy) {
          $actualizado = DB::table('recibos')
                        ->where('periodo', $periodo)
                        ->where('estado', 'PENDIENTE')
                        ->where('retrasoPago', '=', 0)
                        ->count();
          if ($actualizado) {
            $impuesto = DB::table('configuracion_recibos')->where('id', '>=', 1)->value('impuestoRetraso');
            DB::table('recibos')
                ->where('periodo', $periodo)
                ->where('estado', 'PENDIENTE')
                ->where('retrasoPago', '=', 0)
                ->update(['retrasoPago' => $impuesto, 'total' => DB::raw("round(total + {$impuesto})")]);
          }
        }
      }
    }

    public static function cambiarFechaVencimiento($periodo, $vence){
      $fechaVence = Carbon::createFromFormat('Y-m-d', $vence);
      $hoy = Carbon::now();
      if ($fechaVence > $hoy) {
        $actualizado = DB::table('recibos')
                      ->where('periodo', $periodo)
                      ->where('estado', 'PENDIENTE')
                      ->where('retrasoPago', '>', 0)
                      ->count();
        if ($actualizado) {
          $impuesto = DB::table('configuracion_recibos')->where('id', '>=', 1)->value('impuestoRetraso');
          DB::table('recibos')
              ->where('periodo', $periodo)
              ->where('estado', 'PENDIENTE')
              ->update(['retrasoPago' => 0, 'total' => DB::raw("round(total - {$impuesto})")]);
        }
      }
      DB::table('recibos')
      ->where('periodo', $periodo)
      ->update(['vence' => $vence]);
    }

    public static function calcularCuentas($periodo){
        return DB::table('recibos')
                ->select(DB::raw('sum(abonoMedidor) as abonoMedidor, sum(reparacion) as reparacion,
                sum(reactivacionMedidor) as reactivacionMedidor, sum(retrasoPago) as retrasoPago, sum(cargoFijo) as cargoFijo,
                sum(hidrante) as hidrante, sum(metrosConsumidos * valorMetro) as consumo, sum(total) as total'))
                ->where('periodo', $periodo)
                ->get();

    }

    public static function reciboPosterior($idMedidor, $periodo){
      return DB::table('lecturas')
          ->select('lectura', 'periodo')
          ->where('idMedidor', $idMedidor)
          ->where('periodo', '>', $periodo)
          ->first();
    }

    public static function consultaSQL(){
        return $recibos = DB::table('abonados')
        ->join('medidores', 'medidores.idAbonado', '=', 'abonados.id')
        ->join('tipo_de_medidores', 'tipo_de_medidores.id', '=', 'medidores.idTipoDeMedidor')
        ->join('lecturas', 'medidores.id', '=', 'lecturas.idMedidor')
        ->join('recibos', 'lecturas.id', '=', 'recibos.idLectura')
        ->join('configuracion_recibos', 'configuracion_recibos.id', '=', 'recibos.idConfiguracionRecibos')
        ->select('abonados.id as abonado', 'abonados.cedula', 'abonados.nombre', 'abonados.apellido1', 'abonados.apellido2',
                'abonados.direccion', 'medidores.id as medidor', 'medidores.detalle', 'tipo_de_medidores.nombre as tipo', 'tipo_de_medidores.personalizado', 'lecturas.lectura',
                'lecturas.metros', 'lecturas.nota', 'recibos.valorMetro', 'recibos.id', 'recibos.periodo', 'recibos.vence', 'recibos.cargoFijo',
                'recibos.estado', 'reparacion', 'abonoMedidor', 'reactivacionMedidor', 'retrasoPago', 'hidrante', 'recibos.created_at','total');

    }

    public static function toString($old, $new, $comparar = false){
      $detalle = 'Numero de Medidor: ' . $old->idMedidor . "\r\n" .
            'Periodo: ' . $old->periodo . "\r\n" .
            'Estado: ' . $old->estado . "\r\n" .
            'Reparación: ' . $old->reparacion . "\r\n" .
            'Abono de medidor: ' . $old->abonoMedidor . "\r\n" .
            'Reactivación de Medidor: ' . $old->reactivacionMedidor . "\r\n" .
            'Retraso de pago: ' . $old->retrasoPago . "\r\n" .
            'Metros consumidos: ' . $old->metrosConsumidos . "\r\n" .
            'Cargo fijo: ' . $old->cargoFijo . "\r\n" .
            'Hidrante: ' . $old->hidrante . "\r\n" .
            'Valor del metro: ' . $old->valorMetro . "\r\n" .
            'Vence: ' . $old->vence . "\r\n" .
            'Total: ' . $old->total . "\r\n";
      if ($comparar) {
        return $detalle;
      }else {
        return  $detalle .
        "\r\n SE ACTUALIZÓ A: \r\n \r\n" .
        'Numero de Medidor: ' . $new->idMedidor . "\r\n" .
        'Periodo: ' . $new->periodo . "\r\n" .
        'Estado: ' . $new->estado . "\r\n" .
        'Reparación: ' . $new->reparacion . "\r\n" .
        'Abono de medidor: ' . $new->abonoMedidor . "\r\n" .
        'Reactivación de Medidor: ' . $new->reactivacionMedidor . "\r\n" .
        'Retraso de pago: ' . $new->retrasoPago . "\r\n" .
        'Metros consumidos: ' . $new->metrosConsumidos . "\r\n" .
        'Cargo fijo: ' . $new->cargoFijo . "\r\n" .
        'Hidrante: ' . $old->hidrante . "\r\n" .
        'Valor del metro: ' . $old->valorMetro . "\r\n" .
        'Vence: ' . $new->vence . "\r\n" .
        'Total: ' . $new->total . "\r\n";
      }
    }

}
