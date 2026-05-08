<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingAntiInspectElement extends Model
{
    protected $table = 'setting_anti_inspect_element';

    protected $fillable = ['status'];
}
