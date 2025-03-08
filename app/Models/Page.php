<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Page extends Model
{
    use HasFactory;

    const PAGE_IMAGE_PATH = 'pages';

    protected $appends = ['image_url'];

    protected $guarded = [];

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
            return Storage::url(self::PAGE_IMAGE_PATH . DIRECTORY_SEPARATOR . $this->image);
        }
        return null;
    }
    
}
