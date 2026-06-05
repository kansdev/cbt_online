<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WawancaraDeskripsi extends Model
{
    protected $table = "wawancara_deskripsi";

    protected $fillable = [
        'wawancara_id',
        'kategori',
        'deskripsi'
    ];

    public function wawancara() {
        return $this->belongsTo(Wawancara::class);
    }
}
