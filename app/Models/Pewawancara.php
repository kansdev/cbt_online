<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pewawancara extends Model
{
    protected $table = 'pewawancara';

    protected $fillable = [
        'nip',
        'nama'
    ];

    public function wawancara()
    {
        return $this->hasMany(Wawancara::class);
    }
}
