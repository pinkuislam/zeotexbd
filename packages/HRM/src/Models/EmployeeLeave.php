<?php

namespace Oshnisoft\HRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeLeave extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'leave_type_id', 'pay_type', 'contact_number', 'purpose', 'application_date', 'start_date', 'end_date', 'day_count', 'attachment', 'authorized_by', 'approved_by', 'created_by', 'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
