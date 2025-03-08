<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ShippingRate;
use App\Models\DeliveryAgent;
use App\Models\Order;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','date', 'order_code', 'note', 'order_id', 'customer_id', 'reseller_business_id', 'delivery_agent_id', 'shipping_rate_id', 'invoice_number', 'type', 'user_id', 'deduction_amount', 'vat_percent', 'vat_amount', 'discount_amount', 'shipping_charge', 'extra_shipping_charge' ,'advance_amount','subtotal_amount','total_amount','commission_percent', 'commission_amount','reseller_amount', 'status', 'has_return', 'created_by', 'updated_by'
    ];

    protected $qty = 0;
    
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function resellerBusiness()
    {
        return $this->belongsTo(User::class, 'reseller_business_id', 'id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate_id', 'id');
    }

    public function delivery()
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function payment()
    {
        return $this->hasMany(CustomerPayment::class,  'sale_id', 'id');
    }

    public function getsaleConfirmPaymentAttribute()
    {
        return $this->payment()->sum('amount');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function saleReturn()
    {
        return $this->hasMany(SaleReturn::class, 'sale_id', 'id');
        
    }

    public function returnQty()
    {
        foreach ($this->saleReturn as $value) {
            
            $this->qty += $value->items->sum('quantity');
        }
        return $this->qty;
        
    }
}
