<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivityAdmin extends Model
{
    protected $table = 'log_activity_admin';

    protected $fillable = [
        'activity',
        'ip_address',
        'user_agent',
    ];
}
