<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $fillable = [
        'id_siswa',
        'id_soal',
        'jawaban',
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
}
