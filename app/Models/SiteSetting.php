<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    use HasFactory;

    const SITE_SETTING_PATH = 'sitesetting';

    protected $appends = ['logo_url','favicon_url'];

    protected $guarded = [];

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url(self::SITE_SETTING_PATH . DIRECTORY_SEPARATOR . $this->logo);
        }
        return null;
    }

    public function getFaviconUrlAttribute()
    {
        if ($this->favicon) {
            return Storage::url(self::SITE_SETTING_PATH . DIRECTORY_SEPARATOR . $this->favicon);
        }
        return null;
    }
}
