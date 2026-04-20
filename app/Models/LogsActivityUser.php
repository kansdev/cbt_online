<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsActivityUser extends Model
{
    protected $fillable = [
        'id_siswa',
        'activity',
        'ip_address',
        'user_agent',
    ];
}
