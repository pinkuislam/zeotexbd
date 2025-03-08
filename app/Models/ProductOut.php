<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Color;
use App\Models\Unit;

class ProductOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'flagable_id', 'flagable_type', 'product_id', 'color_id', 'unit_id', 'quantity', 'unit_price', 'net_unit_price', 'unit_cost', 'total_price'
    ];

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

    public function getStock()
    {
        $data = ProductIn::where('product_id',$this->product_id)->where('color_id',$this->color_id)->where('status',0)
        ->selectRaw('sum(quantity) as total_qty, sum(used_quantity) as total_used_qty, (sum(quantity)-sum(used_quantity)) as totalstock')
        ->first();
        return $data->totalstock ?? 0;
    }
}
