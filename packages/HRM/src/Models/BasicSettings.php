<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class BasicSettings extends Model
{
    protected $fillable = [
        'name', 'value',
    ];
}
