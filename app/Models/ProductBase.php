<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_id', 'product_id', 'quantity'
    ];

    public function package()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'base_id');
    }
    
    public function productFabric()
    {
        return $this->hasOne(ProductFabric::class, 'product_id', 'base_id');
    }
}
