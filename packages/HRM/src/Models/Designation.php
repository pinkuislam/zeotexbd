<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class Designation extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'name', 'status', 'created_by', 'updated_by'
    ];
}
