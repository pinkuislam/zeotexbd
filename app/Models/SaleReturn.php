<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'code','date', 'note', 'sale_id', 'customer_id', 'reseller_business_id', 'user_id', 'deduction_amount', 'return_amount', 'reseller_amount',  'cost', 'created_by', 'updated_by'
    ];
    
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
    public function items()
    {
        return $this->morphMany(ProductIn::class, 'flagable');
    }
    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function resellerBusiness(){
        return $this->belongsTo(User::class, 'reseller_business_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
