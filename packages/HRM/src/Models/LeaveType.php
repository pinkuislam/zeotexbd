<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class LeaveType extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'name', 'day_count', 'remarks', 'status', 'created_by', 'updated_by'
    ];
}
