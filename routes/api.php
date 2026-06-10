<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CekPesertaController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\WawancaraController;

Route::post('/ujian/cek-peserta', [CekPesertaController::class, 'cek_peserta']);
Route::post('/ujian/persiapan/{id_siswa}', [CekPesertaController::class, 'persiapan_ujian']);
// Route::get('/ujian/data-peserta/{id_siswa}', [ExamController::class, 'mulai_ujian']); 
Route::post('/ujian/mulai/{id_siswa}', [ExamController::class, 'mulai_ujian']);
Route::get('/ujian/soal/{id_siswa}', [ExamController::class, 'halaman_soal']);
Route::post('/ujian/simpan-batch', [ExamController::class, 'simpan_batch']);

// wawancara sesi
Route::post('/wawancara/login/{nip}', [WawancaraController::class, 'login_pewawancara']);
Route::get('/wawancara/{id}', [WawancaraController::class, 'show']);
Route::post('/wawancara/simpan', [WawancaraController::class, 'simpan']);
Route::post('/wawancara/cek/{nomor_pendaftaran}', [WawancaraController::class, 'cek_if_exists']);
Route::get('/wawancara/hasil/pewawancara/{id}', [WawancaraController::class, 'hasil_wawancara']);

