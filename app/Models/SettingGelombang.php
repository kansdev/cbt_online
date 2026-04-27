<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingGelombang extends Model
{
    protected $table = 'setting_gelombang';

    protected $fillable = ['id_gelombang','status'];
}
