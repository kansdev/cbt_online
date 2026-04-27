<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingWaktuTes extends Model
{
    protected $table = 'setting_duration';

    protected $fillable = ['id_gelombang', 'durasi', 'tanggal_mulai'];
}

