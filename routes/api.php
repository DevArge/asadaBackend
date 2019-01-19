<?php

use Illuminate\Http\Request;
//=========================================================================
//=============================== Login ===================================
//=========================================================================
Route::post('login',                   'LoginController@login');
Route::get('renuevatoken',             'LoginController@renuevaToken');
//=========================================================================
//=============================== User ====================================
//=========================================================================
Route::get('usuarios',                   'UserController@getUsers');
Route::get('usuario/{id}',               'UserController@getUser');
Route::get('usuarios/buscar/{termino?}', 'UserController@buscarUsers');
Route::post('usuario',                   'UserController@postUser');
Route::put('usuario/{id}',               'UserController@putUser');
Route::delete('usuario/{id}',            'UserController@deleteUser');
//=========================================================================
//========================= Historial User ================================
//=========================================================================
Route::get('historial',                'HistorialController@getHistoriales');
Route::post('historial',               'HistorialController@postHistorial');

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
//========================= Deudas Medidor ================================
//=========================================================================
Route::post('medidor/deuda/{id}',          'DeudaController@postReparacion');
Route::get('medidor/deudas/{id}',          'DeudaController@getDeudas');
Route::put('medidor/deuda/{id}',           'DeudaController@putDeuda');
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
//=========================================================================
//============================== ASADA ====================================
//=========================================================================
Route::get('asada',                          'AsadaController@getAsada');
Route::put('asada/{id}',                     'AsadaController@putAsada');
//=========================================================================
//=================== Configuracion de Medidores ==========================
//=========================================================================
Route::get('configuracion-medidores',      'ConfMedidorController@getConfMedidores');
Route::put('configuracion-medidores/{id}', 'ConfMedidorController@putConfMedidores');
//=========================================================================
//===================== Configuracion de Recibos ==========================
//=========================================================================
Route::get('configuracion-recibos',      'ConfReciboController@getConfRecibos');
Route::put('configuracion-recibos/{id}', 'ConfReciboController@putConfRecibos');





