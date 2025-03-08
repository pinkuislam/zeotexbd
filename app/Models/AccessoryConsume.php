<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryConsume extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }  
    public function items()
    {
        return $this->morphMany(AccessoryItem::class, 'flagable');
    }
}
