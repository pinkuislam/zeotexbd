<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductOtherImage extends Model
{
    use HasFactory;

    const PRODUCT_IMAGE_PATH = 'products';

    protected $appends = ['image_url'];

    protected $guarded = [];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(self::PRODUCT_IMAGE_PATH . DIRECTORY_SEPARATOR .$this->product_id . DIRECTORY_SEPARATOR . $this->image);
        }
        return null;
    }
}
