<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductOtherInfo extends Model
{
    use HasFactory;
    const PRODUCT_IMAGE_PATH = 'products';

    protected $appends = ['image_url'];

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(self::PRODUCT_IMAGE_PATH . DIRECTORY_SEPARATOR . $this->image);
        }
        return null;
    }
}
