<?php

//=========================================================================
//=========================== Recibo PDF ==================================
//=========================================================================
Route::get('recibo/pdf/{id}',                              'PDFController@getRecibo');
Route::get('reporte/excel/{periodo}',                      'ExcelController@export');
Route::get('reporte/excel/cuenta/{id}/{fechaInicio}/{fechaFin}',  'ExcelController@exportCuentas');
