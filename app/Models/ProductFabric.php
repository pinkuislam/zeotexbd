<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFabric extends Model
{
    protected $fillable = [
        'product_id', 'fabric_product_id', 'fabric_unit_id', 'fabric_quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function fabric()
    {
        return $this->belongsTo(Product::class, 'fabric_product_id');
    }

    public function fabricUnit()
    {
        return $this->belongsTo(Unit::class, 'fabric_unit_id', 'id');
    }
}
