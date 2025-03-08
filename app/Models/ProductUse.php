<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_in_id', 'product_out_id', 'quantity'
    ];

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class, "product_in_id", "id");
    }

    public function productOut()
    {
        return $this->belongsTo(ProductOut::class, "product_out_id", "id");
    }
}
