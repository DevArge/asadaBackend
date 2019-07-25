<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
  <meta name="description" content="ASADA: Los Jocotes - Sitio Web">
  <meta name="author" content="UNA SRCH CL- Ing. Grupo #6 - 2017">

  <title>Sistema de Asadas Comunales (SAC)</title>
</head>

<body>
  <div style="background: #f2f2f2; border: 1px solid #1c2a51; border-radius: 8px; margin: 0px auto; width: 75%;">

    <div style="background: #1c2a51; border-top-left-radius: 8px; border-top-right-radius: 8px; margin: 0px auto; height: 30px;">
      <b style="color: #fff; font-size: 24px; line-height: 30px; text-align: center; padding: 0px 30%;">SAC</b>
    </div>

    <div style="background: #f2f2f2; margin: 0px auto; width:75%;">
      <p><strong>Nombre:</strong> {{$r->nombre}}</p>
      <p><strong>Contacto:</strong> {{$r->contacto}}</p>
      <hr>
      <p><strong>Reporte:</strong>
        <br><br>{{$r->mensaje}}</p>
      </div>

      <div style="background: #1c2a51; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; margin: 0px auto; height: 30px;">
        <p style="color: #fff; line-height: 30px; text-align: right;">SAC&nbsp;</p>
      </div>

    </div>
  </body>
  </html>
