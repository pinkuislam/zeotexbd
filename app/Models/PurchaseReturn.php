<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'date','supplier_id', 'purchase_id', 'type', 'note', 'cost','total_amount','subtotal_amount','approved_at','approved_by','created_by','updated_by'
    ];

    public function purchase(){
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    } 
    public function approvedBy(){
        return $this->belongsTo(User::class, 'approve_by', 'id');
    } 
    public function items()
    {
        return $this->morphMany(ProductOut::class, 'flagable');
    }
}
