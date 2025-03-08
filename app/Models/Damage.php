<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'date', 'type', 'note','created_by','updated_by'
    ];

    public function items()
    {
        return $this->morphMany(ProductOut::class, 'flagable');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
