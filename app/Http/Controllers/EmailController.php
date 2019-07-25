<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Asada;

class EmailController extends Controller{

    public function enviarCorreo(Request $r){
      $asada = Asada::where('id', '>=', 1)->first();
      try {
        Mail::send('mails.email',compact('r','asada'), function($msj)use($asada){
          $msj->subject('Reporte');
          $msj->to($asada->correo);
        });
      } catch (\Exception $e) {
        return response()->json(['ok' => 'no se envio el correo'], 503);
      }
      return response()->json(['ok' => 'se envio el correo'], 200);
    }
}
