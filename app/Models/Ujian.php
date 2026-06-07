<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $fillable = [
        'id_siswa',
        'nisn',
        'status',
        'tahap',
        'mulai_at',
        'waktu_selesai_umum',
        'waktu_selesai_jurusan_pertama',
        'waktu_selesai_jurusan_kedua',
        'selesai_at'
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'waktu_selesai_umum' => 'datetime',
        'waktu_selesai_jurusan_pertama' => 'datetime',
        'waktu_selesai_jurusan_kedua' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'id_siswa', 'id');
    }
}
