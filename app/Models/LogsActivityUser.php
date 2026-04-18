<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsActivityUser extends Model
{
    protected $table = 'logs_activity_user';

    protected $fillable = [
        'id_siswa',
        'activity',
        'ip_address',
        'user_agent',
    ];
}
