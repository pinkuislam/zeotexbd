<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeAttendance extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'attendance_date', 'login_time', 'logout_time', 'in_latitude', 'in_longitude', 'in_address', 'in_note',
        'out_latitude', 'out_longitude', 'out_address', 'out_note', 'in_image', 'in_image_url', 'out_image', 'out_image_url',
        'is_late', 'is_early', 'created_by', 'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
