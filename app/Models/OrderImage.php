<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderImage extends Model
{
    use HasFactory;

    const ORDER_IMAGE_PATH = 'orders';

    protected $fillable = ['order_id', 'image'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(self::ORDER_IMAGE_PATH . DIRECTORY_SEPARATOR . $this->image);
        }
        return null;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
