<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\SettingController;

Route::prefix('apps_ade')->group(function() {    
    Route::middleware('guest')->group(function() {
        Route::get('/', [AuthController::class, 'login'])->name('login');
        Route::post('/login/process', [AuthController::class, 'login_process'])->name('admin.login_process');
    });
        
    Route::middleware('auth', 'prevent-back-history', 'session-timeout')->group(function() {
        // Get    
        // Beranda
        Route::get('/beranda', [AdminController::class, 'index'])->name('admin.index');
        
        // Participant
        Route::get('/peserta', [ParticipantController::class, 'peserta'])->name('admin.peserta');
        Route::get('/aktif-peserta', [ParticipantController::class, 'peserta_aktif'])->name('admin.aktif_peserta');
        Route::get('/reset-peserta', [ParticipantController::class, 'reset_peserta'])->name('admin.reset_peserta');
        Route::get('/aktif-peserta/nonaktifkan-peserta/{id}', [ParticipantController::class, 'nonaktifkan_peserta'])->name('admin.aktif_peserta.one_nonaktif');
        Route::get('/aktif-peserta/aktifkan-peserta/{id}', [ParticipantController::class, 'aktifkan_peserta'])->name('admin.aktif_peserta.one_aktif');
        Route::get('/reset/{id}', [ParticipantController::class, 'reset'])->name('admin.reset');

        // Exam
        Route::get('/soal', [ExamController::class, 'soal'])->name('admin.soal');
        Route::get('/koreksi', [ExamController::class, 'koreksi'])->name('admin.koreksi');
        Route::get('/riwayat', [ExamController::class, 'riwayat'])->name('admin.riwayat');
        Route::get('/export-excel', [ExamController::class, 'unduh_hasil_jawaban'])->name('admin.unduh_hasil_jawaban');

        //Setting
        Route::get('/settings', [SettingController::class, 'settings'])->name('admin.settings');
        
        // Auth
        Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        
        // Post    
        Route::post('/peserta/tambah-peserta', [ParticipantController::class, 'tambah_peserta'])->name('admin.tambah_peserta');
        Route::post('/import-soal', [ExamController::class, 'importSoal'])->name('admin.import-soal');
        Route::post('/import-peserta', [ParticipantController::class, 'importPeserta'])->name('admin.import-peserta');
        Route::post('/setting/durasi', [SettingController::class, 'settings_waktu_tes'])->name('admin.setting_durasi');
        Route::post('/setting/gelombang', [SettingController::class, 'settings_gelombang'])->name('admin.setting_gelombang');
        Route::post('/setting/anti_inspect_element', [SettingController::class, 'settings_anti_inspect_element'])->name('admin.setting_anti_inspect_element');
        

        // Put | Update
        Route::put('/aktif-peserta/aktifkan', [ParticipantController::class, 'aktifkan_seluruh_peserta'])->name('admin.aktif_peserta.aktif');
        Route::put('/aktif-peserta/aktifkan-pergelombang', [ParticipantController::class, 'aktifkan_peserta_pergelombang'])->name('admin.aktif_peserta.pergelombang');
        Route::put('/aktif-peserta/nonaktifkan-pergelombang', [ParticipantController::class, 'nonaktifkan_peserta_pergelombang'])->name('admin.nonaktif_peserta.pergelombang');
        Route::put('/aktif-peserta/nonaktifkan', [ParticipantController::class, 'nonaktifkan_seluruh_peserta'])->name('admin.aktif_peserta.nonaktif');
        
        // Delete 
        Route::delete('/clear_log', [AdminController::class, 'clear_log'])->name('admin.clear_log');  
    });
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
