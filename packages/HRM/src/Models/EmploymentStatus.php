<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmploymentStatus extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'designation_id', 'department_id', 'work_station_id', 'supervisor_id',
        'effect_date', 'probation_end_on', 'remarks', 'status', 'created_by', 'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function workStation()
    {
        return $this->belongsTo(WorkStation::class, 'work_station_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id', 'id');
    }
}
