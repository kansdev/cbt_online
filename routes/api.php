<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CekPesertaController;
use App\Http\Controllers\Api\ExamController;

Route::post('/ujian/cek-peserta', [CekPesertaController::class, 'cek_peserta']);
Route::post('/ujian/persiapan/{id_siswa}', [CekPesertaController::class, 'persiapan_ujian']);
// Route::get('/ujian/data-peserta/{id_siswa}', [ExamController::class, 'mulai_ujian']); 
Route::post('/ujian/mulai/{id_siswa}', [ExamController::class, 'mulai_ujian']);
Route::get('/ujian/soal/{id_siswa}', [ExamController::class, 'halaman_soal']);
Route::post('/ujian/simpan-batch', [ExamController::class, 'simpan_batch']);

