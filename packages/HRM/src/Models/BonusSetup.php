<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class BonusSetup extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'title', 'percent_type', 'percent', 'bonus_date', 'status', 'created_by', 'updated_by',
    ];
}
