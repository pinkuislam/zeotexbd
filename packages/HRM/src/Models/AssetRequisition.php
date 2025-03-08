<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRequisition extends Model
{
    protected $fillable = [
        'employee_id', 'date', 'expected_date', 'item', 'note', 'quantity', 'status', 'feedback', 'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(config('hrm.models.updated_by'), 'updated_by', 'id');
    }
}
