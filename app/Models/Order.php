<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'date', 'customer_id', 'reseller_business_id', 'type', 'user_id', 'shipping_rate_id', 'delivery_agent_id', 'shipping_charge', 'advance_amount', 'bank_id', 'discount_amount', 'note', 'amount', 'status', 'has_stock_done', 'created_by', 'updated_by'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(OrderImage::class, 'order_id', 'id');
    }

    public function advance()
    {
        return $this->hasMany(CustomerPayment::class,  'order_id', 'id');
    }
    public function sale()
    {
        return $this->hasOne(Sale::class, 'order_id', 'id');
    }
    public function getcustomerPaymentAttribute()
    {
        return $this->advance->sum('amount');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function resellerBusiness()
    {
        return $this->belongsTo(User::class, 'reseller_business_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate_id', 'id');
    }

    public function delivery()
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

}
