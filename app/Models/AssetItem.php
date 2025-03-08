<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model
{
    use HasFactory;
    protected $fillable = [
         'date','note','asset_id', 'quantity', 'price','total_amount'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
