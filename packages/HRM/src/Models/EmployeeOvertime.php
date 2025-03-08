<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeOvertime extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'policy_id', 'date', 'note', 'amount', 'status',
        'approve_at', 'created_by', 'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function policy()
    {
        return $this->belongsTo(OvertimePolicy::class, 'policy_id', 'id');
    }
}
