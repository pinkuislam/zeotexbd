<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryStockReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
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
    public function purchase(){
        return $this->belongsTo(AccessoryStock::class, 'purchase_id', 'id');
    }
}
