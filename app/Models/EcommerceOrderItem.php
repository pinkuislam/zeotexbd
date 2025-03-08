<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrderItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ecommerceOrder()
    {
        return $this->belongsTo(EcommerceOrder::class,'ecommerce_order_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
