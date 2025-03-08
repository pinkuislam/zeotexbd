<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id', 'product_id', 'unit_id', 'color_id', 'quantity', 'reseller_unit_price', 'unit_price', 'amount'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function items()
    {
        return $this->morphMany(ProductOut::class, 'flagable');
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

    public function basePackages()
    {
        return $this->hasMany(BasePackage::class, 'package_product_id', 'product_id');
    }
}
