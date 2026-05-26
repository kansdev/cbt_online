<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalAcak extends Model
{

    protected $fillable = [
        'id_siswa',
        'id_soal',
        'urutan',
        'tahap'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'id_siswa', 'id');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal', 'id');
    }

    // Tambahkan ini jika Anda ingin menggunakan eager loading jawaban nanti
    public function jawaban_user()
    {
        return $this->hasOne(Jawaban::class, 'id_soal', 'id_soal')
                    ->where('id_siswa', $this->id_siswa);
    }
}
