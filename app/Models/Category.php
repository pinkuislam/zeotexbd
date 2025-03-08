<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;
    const CATEGORY_IMAGE_PATH = 'categories';

    protected $appends = ['image_url'];

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function childs()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(ProductOtherInfo::class,'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(self::CATEGORY_IMAGE_PATH . DIRECTORY_SEPARATOR . $this->image);
        }
        return null;
    }
}
