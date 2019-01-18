<?php

use Illuminate\Http\Request;
//=========================================================================
//=============================== Login ===================================
//=========================================================================


//=========================================================================
//============================== Abonado ==================================
//=========================================================================
Route::get('abonados',                   'AbonadoController@getAbonados');
Route::get('abonado/{id}',               'AbonadoController@getAbonado');
Route::get('abonados/buscar/{termino?}', 'AbonadoController@buscarAbonados');
Route::post('abonado',                   'AbonadoController@postAbonado');
Route::put('abonado/{id}',               'AbonadoController@putAbonado');
Route::delete('abonado/{id}',            'AbonadoController@deleteAbonado');
//=========================================================================
//============================== TipoMedidor ==============================
//=========================================================================
Route::get('tiposDeMedidores', 'TipoMedidorController@getTipodeMedidores');
Route::post('tipoDeMedidor',   'TipoMedidorController@postTipoMedidor');
//=========================================================================
//============================== Medidor ==================================
//=========================================================================
Route::get('medidores',                   'MedidorController@getMedidores');
Route::get('medidores/buscar/{termino?}', 'MedidorController@buscarMedidores');
Route::get('medidor/{id}',                'MedidorController@getMedidor');
Route::post('medidor',                    'MedidorController@postMedidor');
Route::put('medidor/{id}',                'MedidorController@putMedidor');
Route::delete('medidor/{id}',             'MedidorController@deleteMedidor');
Route::post('medidor/habilitar/{id}',     'MedidorController@habilitarMedidor');
//=========================================================================
//======================= Reparacion Medidor ==============================
//=========================================================================
Route::post('medidor/reparacion/{id}', 'ReparacionController@postReparacion');
//=========================================================================
//============================== Lectura ==================================
//=========================================================================
Route::get('lecturas',                         'LecturaController@getLecturas');
Route::get('lecturas/medidor/{id}',            'LecturaController@getLecturasMedidor');
Route::get('lecturas/buscar/{tipo}/{termino?}','LecturaController@buscarLecturas');
Route::post('lectura',                         'LecturaController@postLectura');
Route::delete('lectura/{id}',                  'LecturaController@deleteLectura');
//=========================================================================
//============================== Recibo ===================================
//=========================================================================
Route::get('recibos',                         'ReciboController@getRecibos');
Route::get('recibos/cuentas',                 'ReciboController@getCuentasRecibos');
Route::get('recibos/medidor/{id}',            'ReciboController@getRecibosMedidor');
Route::get('recibos/buscar/{tipo}/{termino?}','ReciboController@buscarRecibos');
Route::put('recibo/{id}',                     'ReciboController@putRecibo');
Route::delete('recibo/{id}',                  'ReciboController@deleteRecibo');


