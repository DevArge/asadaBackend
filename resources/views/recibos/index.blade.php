<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">

  <title> ASADA:  </title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

  <!-- Styles -->
  <style>

  @charset "UTF-8";
  @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

  html {
    font-family: sans-serif;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
    margin-top: 0.3em;
    margin-left: 0.6em;
    margin-right: 0.6em;
  }

  .main-container{
    width: 100%;
  }

  table {
    color: #555;
    font-size: 10px;
  }

  .section-container{
    margin: 0px 0.25%;
    width: 100%;
  }

  .aside-container{
    margin: 0px 0.25%;
  }

  .header-title{
    font-size: 12px;
    text-align: center;
  }

  .panel-main{
    background  : #fff;
    border: 1px solid;
    border-color: #555;
    border-radius: 4px;
    min-height: 600px;
  }

  .panel-header {
    background: #fafafa;
    border-radius: 4px;
    color: #555;
    font-size: 12px;
    padding: 0.5px 2%;
  }

  .panel-body{
    color: #222;
    padding: 0px 2%;
  }

  .item-container{
    margin: 5px;
    padding: 0.5px 2.5%;
  }
  .total-apagar{
	  font-size: 12px;
	  font-weight: bold
  }

  </style>


</head><body style="background: #FFF">

  <?php
	$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
	$vari = explode("-", $recibo->periodo);
	$peri = $meses[(intval($vari[1])-1)] . " de " . $vari[0];
	//fecha de vencimiento
	$matriz = explode("-", $recibo->vence);
	$mesVencimiento = $matriz[2] . " de " . $meses[(intval($matriz[1])-1)] . " de " . $matriz[0];
  ?>

  <div class="main-container">
    <table> <!-- ABRE - TABLA RECIBO COMPLETO -->
      <tr>
        <td width='70%'><!-- ABRE - TABLA RECIBO DESGLOSE -->
          <div class="section-container">

            <div class="panel-main">
              <div class="panel-header">
                ASADA: {{$asada->nombre}} / Ced Jurídica: {{$asada->cedulaJuridica}} / Teléfono: {{$asada->telefono}}
              </div>

              <div class="panel-body" style="height:440px;">
                <table style="">
                  <tr><!-- ABRE - DATOS ABONADO -->
                    <td>
                      <table width="200">
                        <tr>
                          <td><b>N° Cedula: </b> {{$recibo->cedula}}</td>
                        </tr>

                        <tr>
                          <td><b>Abonado: </b> {{$recibo->abonado . " - " . $recibo->nombre . " " . $recibo->apellido1 . " " . $recibo->apellido2}}</td>
                        </tr>

                        <tr>
                          <td> </td>
                        </tr>

                        <tr>
                          <td><b>Dirección: </b> {{$recibo->direccion}}</td>
                        </tr>
                      </table>
                    </td>

                    <td>
                      <table align="center">
                        <tr>
                          <td><b>N° Medidor: </b> {{$recibo->medidor}}</td>
                        </tr>

                        <tr>
                          <td><b>Tipo de Medidor: </b> {{$recibo->tipo}}</td>
                        </tr>

                        <tr>
                          <td><div style="border-bottom: 0.5px solid #555;"></div></td>
                        </tr>

                        <tr>
                          <td><b>Período: </b> {{$peri}}</td>
                        </tr>

                        <tr>
                          <td><b>N° Recibo: </b> {{$recibo->id}}</td>
                        </tr>

                        <tr>
                          <td><b>VENCE: {{$mesVencimiento }}</b></td>
                        </tr>
                      </table>
                    </td>
                  </tr><!-- CIERRA - DATOS ABONADO -->
                  <tr>
                    <td colspan="2">
                      <div class="panel-main">

                        <div class="panel-header">
                          <div class="header-title">
                            Notificación
                          </div>
                        </div>

                        <div class="panel-body">
                          <div class="item-container" style="color: #555; font-size: 10px;">
                          @if(\Carbon\Carbon::now() >= $configuracion->fechaInicio && \Carbon\Carbon::now() <= $configuracion->fechaFin)
                            {{$configuracion->notificacion}}
                          @else
                            {{$configuracion->notificacionDefault}}
                          @endif
                          </div>
                        </div>

                      </div>
                    </td>
                  </tr>

                  <tr style="margin-top: 0px;"><!-- ABRE - DETALLES CONSUMO -->
                    <td> <!-- ABRE - PANEL CONSUMO 2 -->
                      <div class="panel-main" style="min-height: 5%;">

                        <div class="panel-header">
                          <div class="header-title">
                            Información de Consumo
                          </div>
                        </div>

                        <div class="panel-body" style="height:35px;">
                          <div>
                            <table width='100%' style="font-size:10px; text-align:center;">
                              <tr>
                                <td><b>Lect Ant</b></td>
                                <td><b>Lect Act</b></td>
                                <td><b>Consumo</b></td>
                                <td><b>Valor</b></td>
                              </tr>
                              <tr>
                                <td>{{$recibo->lectura - $recibo->metros}}³</td>
                                <td>{{$recibo->lectura}}³</td>
                                <td>{{$recibo->metros}}³</td>
                                <td>¢{{$recibo->valorMetro}}</td>
                              </tr>
                            </table>
                          </div>
                        </div>

                      </div>
                      @if($lecturasAnteriores->count())
                      <div class="panel-main" style="margin-top: 4px;">

                        <div class="panel-header">
                          <div class="header-title">
                            Historial de Consumo
                          </div>
                        </div>

                        <div class="panel-body" style="height: auto;">
                          <table width='100%' style="font-size: 10px; text-align:center;">
                            <tr>
                              <td><b>Periodo</b></td>
                              <td><b>Consumo</b></td>
                            </tr>
                            @foreach ($lecturasAnteriores as $lectura)
                            <tr>
                              <td>{{$lectura->periodo}}</td>
                              <td>{{$lectura->metros}}</td>
                            </tr>
                            @endforeach
                          </table>
                        </div>

                      </div>
                      @endif
                    </td><!-- CIERRA - PANEL CONSUMO 2 -->
                    <td>
                      <table width='100%'>
                        <tr><!-- ABRE - DESGLOSE -->
                          <td>
                            <div class="panel-main" style="min-height: 30%;">

                              <div class="panel-header">
                                <div class="header-title">
                                  Detalles de Consumo
                                </div>
                              </div>

                              <div class="panel-body">
                                <table style="font-size:10px;" width='100%'>
                                  <tr>
                                    <td width='80%'><b>Descripcion</b></td>
                                    <td width='20%'><b>Total<b></td>
                                    </tr>

                                      <tr>
                                        <td width='70%'>CARGO FIJO DE AGUA:</td>
                                        <td width='20%'>¢{{$recibo->cargoFijo}}</td>
                                      </tr>

                                   @if (!$recibo->personalizado)
                                    <tr>
                                        <td width='80%'>HIDRANTE:</td>
                                        <td width='20%'>¢{{$recibo->hidrante}}</td>
                                    </tr>

                                    <tr>
                                        <td width='80%'>CONSUMO DE AGUA:</td>
                                        <td width='20%'>¢{{$recibo->metros * $recibo->valorMetro}}</td>
                                    </tr>
                                      @if($recibo->reactivacionMedidor > 0)
                                        <tr>
                                            <td>RECONEXIÓN:</td>
                                            <td>¢{{$recibo->reactivacionMedidor}}</td>
                                        </tr>
                                      @endif
                                      @if($recibo->abonoMedidor > 0)
                                        <tr>
                                          <td>ABONO DE MEDIDOR: </td>
                                          <td>¢{{$recibo->abonoMedidor}}</td>
                                        </tr>
                                      @endif
                                      @if($recibo->reparacion > 0)
                                      <tr>
                                        <td>REPARACIÓN DEL MEDIDOR:</td>
                                        <td>¢{{$recibo->reparacion}}</td>
                                      </tr>
                                      @endif
                                      @if($recibo->retrasoPago > 0)
                                      <tr>
                                        <td>MULTA POR RETRASO:</td>
                                        <td>¢{{$recibo->retrasoPago}}</td>
                                      </tr>
                                      @endif
                                    @endif
                                    <tr>
                                      <td width='100%' style="border-bottom: 1px dashed; border-color: #555;"></td>
                                      <td  style="border-bottom: 1px dashed; border-color: #555;"></td>
                                    </tr>

                                    <tr>
                                      <td width='80%'><b>TOTAL A PAGAR:</b></td>
                                      <td width='30%'><span class="total-apagar">¢{{$recibo->total}}</span></td>
                                    </tr>
                                  </table>
                                </div>
                              </div>
                            </td>
                          </tr><!-- CIERRA - DESGLOSE -->
                        </table>
                      </td>
                    </tr><!-- CIERRA - DETALLES CONSUMO -->

                    <tr><!-- ABRE - NOTIFICACION -->
                      @if($recibosPendientes->count())
                      <td width='50%'><!-- ABRE - MESES PENDIENTES -->
                        <div class="panel-main" style="min-height: 15%;">

                          <div class="panel-header">
                            <div class="header-title">
                              Meses Pendientes
                            </div>
                          </div>

                          <div class="panel-body">
                            <table width='100%' style="font-size:10px">
                              <tr style="text-align:center;">
                                <td width='100%'>Si ya ha cancelado previamente su servicio, omita este aviso.</td>
                              </tr>
                            </table>

                            <table width='100%' style="font-size:10px; text-align: center;">
                              <tr>
                                <td><b>Periodo</b></td>
                                <td><b>Monto</b></td>
                              </tr>
                            </table>
                              <table width='100%' style="font-size:10px; text-align: center;">
                                @foreach ($recibosPendientes as $pendi)
                                    <tr style="text-align: center">
                                        <td>{{$pendi->periodo}}</td>
                                        <td>¢{{$pendi->total}}</td>
                                    </tr>
                                @endforeach
                              </table>
                          </div>
                        </div>
                      </td><!-- CIERRA - MESES PENDIENTES -->
                      @endif
                      @if ($recibo->nota != null)
                        <td><!-- ABRE - NOTAS-->
                          <div class="panel-main" style="min-height: 15%;">

                            <div class="panel-header">
                              <div class="header-title">
                                Nota de la lectura de este periodo
                              </div>
                            </div>

                            <div class="panel-body">

                              <table width='100%' style="font-size:10px; text-align: center;">
                                <tr>
                                  <td>{{$recibo->nota}}</td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </td><!-- CIERRA - NOTAS-->
                      @endif
                    </tr><!-- CIERRA - NOTIFICACION -->
                  </table>
                </div>
              </div>
            </div>
        </td> <!-- CIERRA - TABLA RECIBO DESGLOSE -->

          <!-- ABRE - TABLA RECIBO COLILLA -->
          <td width='30%'>
            <div class="aside-container">

              <div class="panel-main">

                <div class="panel-header">
                  <div class="header-title">
                    ASADA: {{$asada->nombre}}
                  </div>
                </div>

                <div class="panel-body"style="height:440px;">

                  <div class="item-container" style="color: #555; font-size:10px;">
                    <b>N° Cedula: </b> {{$recibo->cedula}}<br>
                    <b>Abonado: </b> {{$recibo->abonado . " - " . $recibo->nombre . " " . $recibo->apellido1 . " " . $recibo->apellido2}}<br><br>
                    <b>Dirección: </b><br> {{$recibo->direccion}}<br><br>
                    <div style="border-bottom: 0.5px solid #555;"></div><br>
                    <b>N° Medidor: </b> {{$recibo->medidor}}<br>
                    <b>Tipo de Medidor: </b> {{$recibo->tipo}}<br><br>
                    <div style="border-bottom: 0.5px solid #555;"></div><br>
                    <b>Período: </b> {{$peri}}<br>
                    <b>N° Recibo: </b> {{$recibo->id}}<br>
                    <b>VENCE: {{$mesVencimiento }}</b><br><br>
                  </div>

                  <div class="item-container">

                    <div class="panel-main" style="min-height: 20%;">

                      <div class="panel-header">
                        <div class="header-title">
                          Detalles de Consumo
                        </div>
                      </div>

                      <div class="panel-body">
                        <table style="font-size:10px;" width='100%'>
                          <tr>
                            <td width='80%'><b>Descripcion</b></td>
                            <td width='20%'><b>Total<b></td>
                            </tr>
                            <tr>
                              <td width='70%'>CARGO FIJO DE AGUA:</td>
                              <td width='20%'>¢{{$recibo->cargoFijo}}</td>
                            </tr>

                          @if (!$recibo->personalizado)
                            <tr>
                              <td width='80%'>HIDRANTE:</td>
                              <td width='20%'>¢{{$recibo->hidrante}}</td>
                            </tr>
                            <tr>
                              <td width='80%'>CONSUMO DE AGUA:</td>
                              <td width='20%'>¢{{$recibo->metros * $recibo->valorMetro}}</td>
                            </tr>
                            @if($recibo->reactivacionMedidor > 0)
                                <tr>
                                <td>RECONEXIÓN:</td>
                                <td>¢{{$recibo->reactivacionMedidor}}</td>
                                </tr>
                            @endif
                            @if($recibo->abonoMedidor > 0)
                                <tr>
                                    <td>ABONO DE MEDIDOR: </td>
                                    <td>¢{{$recibo->abonoMedidor}}</td>
                                </tr>
                            @endif
                            @if($recibo->reparacion > 0)
                              <tr>
                                <td>REPARACIÓN DEL MEDIDOR:</td>
                                <td>¢{{$recibo->reparacion}}</td>
                              </tr>
                            @endif
                            @if($recibo->retrasoPago > 0)
                              <tr>
                                <td>MULTA POR ATRASO:</td>
                                <td>¢{{$recibo->retrasoPago}}</td>
                              </tr>
                            @endif
                          @endif
                            <tr>
                              <td width='100%' style="border-bottom: 1px dashed; border-color: #555;"></td>
                              <td style="border-bottom: 1px dashed; border-color: #555;"></td>
                            </tr>

                            <tr>
                              <td width='80%'><b>TOTAL A PAGAR:</b></td>
                              <td width='30%'><span class="total-apagar">¢{{$recibo->total}}</span></td>
                            </tr>
                          </table>
                        </div>

                      </div>
                    </div>

                    <br>

                  </div>

                </div>

              </div>
            </td><!-- CIERRA - TABLA RECIBO COLILLA -->

          </table><!-- CIERRA - TABLA RECIBO COMPLETO -->

  </div>
</body></html>
