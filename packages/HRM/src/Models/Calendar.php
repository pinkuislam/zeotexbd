<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class Calendar extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'date',
        'in_time',
        'out_time',
        'working_hours',
        'note',
        'status',
        'created_by',
        'updated_by'
    ];
}
