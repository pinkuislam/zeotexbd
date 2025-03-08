<?php

namespace Oshnisoft\HRM\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class Employee extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'name', 'father_name', 'mother_name', 'remarks', 'employee_no', 'contact_no', 'email', 'birth_date', 'gender',
        'org_joining_date', 'religion', 'nationality', 'blood_group', 'present_address', 'permanent_address', 'image',
        'nid_front_image', 'nid_back_image', 'status', 'created_by', 'updated_by'
    ];

    public function workExperiences()
    {
        return $this->hasMany(EmployeeExperience::class, 'employee_id', 'id');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class, 'employee_id', 'id');
    }

    public function employments()
    {
        return $this->hasMany(EmploymentStatus::class, 'employee_id', 'id');
    }

    public function employmentStatus()
    {
        return $this->hasOne(EmploymentStatus::class, 'employee_id', 'id')->latestOfMany();
    }

    public function salary()
    {
        return $this->hasOne(EmployeeSalarySetup::class, 'employee_id', 'id')->latestOfMany();
    }
    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id', 'id');
    }
}
