<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'nomor_registrasi',
        'nama',
        'nisn',
        'jenis_kelamin',
        'jurusan_pertama',
        'jurusan_kedua',
        'jenis_umum',
        'tanggal_kelahiran',
        'id_gelombang',
        'status'
    ];

    public function log_activity_users() {
        return $this->has_one(LogsActivityUser::class, 'id_siswa', 'id');
    }

    public function ujian()
    {
        return $this->hasOne(Ujian::class, 'id_siswa', 'id');
    }

    public function soalAcak()
    {
        return $this->hasMany(SoalAcak::class, 'id_siswa', 'id');
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'id_siswa', 'id');
    }
}
