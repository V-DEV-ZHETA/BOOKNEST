<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description', 'default_value', 'validation_rule'];
}
