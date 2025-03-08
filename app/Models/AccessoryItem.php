<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }
}
