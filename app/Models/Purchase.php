<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Supplier;
use App\Models\ProductIn;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'order_id', 'date','supplier_id', 'challan_number', 'challan_image', 'type', 'note','vat_percent','vat_amount', 'cost', 'adjust_amount', 'total_amount','subtotal_amount','approved_at','approved_by','created_by','updated_by'
    ];

    public function items()
    {
        return $this->morphMany(ProductIn::class, 'flagable');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }  
}
