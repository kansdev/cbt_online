<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WawancaraDetail extends Model
{
    protected $table = "wawancara_details";

    protected $fillable = [
        'wawancara_id',
        'kode_pertanyaan',
        'skor',
    ];

    public function wawancara() {
        return $this->belongsTo(Wawancara::class);
    }
}
