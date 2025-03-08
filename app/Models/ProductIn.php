<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Color;
use App\Models\Unit;

class ProductIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'flagable_id', 'flagable_type', 'product_id', 'color_id', 'unit_id', 'quantity', 'unit_price', 'fabric_unit_id', 'fabric_unit_price', 'fabric_quantity', 'total_price', 'used_quantity', 'cost', 'actual_unit_price', 'return_quantity', 'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function fabricUnit()
    {
        return $this->belongsTo(Unit::class,'fabric_unit_id', 'id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
