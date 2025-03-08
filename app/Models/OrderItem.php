<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'unit_id', 'color_id', 'quantity', 'unit_price', 'amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function productFabric()
    {
        return $this->hasOne(ProductFabric::class, 'product_id', 'product_id');
    }

    public function productBases()
    {
        return $this->hasMany(ProductBase::class, 'product_id', 'product_id');
    }

    public function getStock()
    {
        $data = ProductIn::where('product_id', $this->product_id)->where('color_id', $this->color_id)
            ->selectRaw('sum(quantity) as total_qty, sum(used_quantity) as total_used_qty, (sum(quantity)-sum(used_quantity)) as totalstock')
            ->first();
        return $data->totalstock ?? 0;
    }
}
