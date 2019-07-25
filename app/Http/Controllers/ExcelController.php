<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Recibo;
use App\Factura;

class ExcelController extends Controller{

  private $excel;

  public function __construct(Excel $excel){
     $this->excel = $excel;
     // $this->middleware('jwt.auth');
     // $this->middleware('secretaria');
  }

  public function export(Request $r, $periodo){
    session_start();
    $_SESSION["periodo"]=$periodo;
    return $this->excel->download(new Recibo, 'Cuentas por cobrar.xls');
  }

  public function exportCuentas(Request $r, $id, $fechaInicio, $fechaFin){
    session_start();
    $_SESSION["id"]=$id;
    $_SESSION["fechaInicio"]=$fechaInicio;
    $_SESSION["fechaFin"]=$fechaFin;
    return $this->excel->download(new Factura, 'Reporte de facturas.xls');
  }

}
