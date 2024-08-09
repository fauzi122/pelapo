<?php
// routes/badan_usaha/EksporImpor.php

use App\Http\Controllers\bu\EksportImportController;

Route::controller(EksportImportController::class)->group(function () {
  Route::get('/eksport-import', 'index');
  route::get('/eksport-import/show/{id}/{eix}', 'show_eix');
  Route::post('/simpan_export', 'simpan_exportx');
  Route::put('/update_export/{id}', 'update_exportx');
  Route::delete('/hapus_export/{id}', 'hapus_exportx');
  Route::delete('/hapus_bulan_export/{bulan}', 'hapus_bulan_exportx');
  Route::get('/get-export/{id}', 'get_export');
  Route::put('/submit_export/{id}', 'submit_exportx');
  Route::put('/submit_bulan_export/{bulan}', 'submit_bulan_exportx');
  Route::post('/simpan_import', 'simpan_importx');
  Route::delete('/hapus_import/{id}', 'hapus_importx');
  Route::delete('/hapus_bulan_import/{bulan}', 'hapus_bulan_importx');
  Route::get('/get-import/{id}', 'get_import');
  Route::put('/update_import/{id}', 'update_importx');
  Route::get('/get_kota_import/{kabupaten_kota}', 'get_kota');
  Route::put('/submit_import/{id}', 'submit_importx');
  Route::put('/submit_bulan_import/{bulan}', 'submit_bulan_importx');
  Route::get('/get-negara', 'get_negara');
  Route::post('/import_eksport', 'import_eksportx');
  Route::post('/import_import', 'import_importx');
});
