<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class WorkStation extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'name', 'address', 'status', 'created_by', 'updated_by'
    ];
}
