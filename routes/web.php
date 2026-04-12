<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;

Route::prefix('ujian')->group(function() {
    Route::get('/', function() {
        return view('test/cek');
    })->name('ujian.index');

    Route::post('/cek_peserta', [UserController::class, 'cek_peserta'])->name('ujian.cek_peserta');
    Route::get('/mulai/{id}', [UserController::class, 'mulai_ujian'])->name('ujian.mulai');
    Route::get('/soal/{id}', [UserController::class, 'halaman_soal'])->name('ujian.soal');
    Route::post('/simpan_jawaban', [UserController::class, 'simpan_jawaban'])->name('ujian.simpan_jawaban');
    Route::post('/reset/{id}', [UserController::class, 'reset_ujian'])->name('ujian.reset');
});
