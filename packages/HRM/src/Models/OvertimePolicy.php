<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class OvertimePolicy extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'name', 'amount', 'status', 'created_by', 'updated_by',
    ];
}
