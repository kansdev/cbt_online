<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admin\AdminController;

Route::prefix('apps_ade')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/peserta', [AdminController::class, 'peserta'])->name('admin.peserta');
    Route::get('/soal', [AdminController::class, 'soal'])->name('admin.soal');
    Route::get('/koreksi', [AdminController::class, 'koreksi'])->name('admin.koreksi');
    Route::get('/aktif-peserta', [AdminController::class, 'peserta_aktif'])->name('admin.aktif_peserta');
    Route::get('/riwayat', [AdminController::class, 'riwayat'])->name('admin.riwayat');
    Route::post('/import-soal', [AdminController::class, 'importSoal'])->name('admin.import-soal');
});


Route::prefix('ujian')->group(function() {
    Route::get('/', function() {
        return view('test.cek');
    })->name('ujian.index');

    Route::post('/cek_peserta', [UserController::class, 'cek_peserta'])->name('ujian.cek_peserta');
    Route::get('/mulai/{id}', [UserController::class, 'mulai_ujian'])->name('ujian.mulai');
    Route::get('/soal/{id}', [UserController::class, 'halaman_soal'])->name('ujian.soal');
    Route::post('/simpan_jawaban', [UserController::class, 'simpan_jawaban'])->name('ujian.simpan_jawaban');
    Route::post('/reset/{id}', [UserController::class, 'reset_ujian'])->name('ujian.reset');
});
