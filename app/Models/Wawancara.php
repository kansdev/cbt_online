<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wawancara extends Model
{
    protected $table = "wawancara";

    protected $fillable = [
        'pewawancara_id',
        'user_id',
        'nomor_pendaftaran',
        'catatan',
        'kesimpulan'
    ];

    public function pewawancara()
    {
        return $this->belongsTo(Pewawancara::class);
    }

    public function details() {
        return $this->hasMany(WawancaraDetail::class);
    }

    public function deskripsi() {
        return $this->hasOne(WawancaraDeskripsi::class);
    }
}
