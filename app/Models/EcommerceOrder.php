<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ecommerceOrders()
    {
        return $this->hasMany(EcommerceOrderItem::class,'ecommerce_order_id');
    }
    public function ecommerceOrderImages()
    {
        return $this->hasMany(EcommerceOrderImage::class,'ecommerce_order_id');
    }
    public function shipping()
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate_id', 'id');
    }
}
