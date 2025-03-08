<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Production extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'order_code', 'order_id', 'date', 'note', 'created_by', 'updated_by'
    ];

    public function raw_items()
    {
        return $this->morphMany(ProductOut::class, 'flagable');
    }
    
    public function prod_items()
    {
        return $this->morphMany(ProductIn::class, 'flagable');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
