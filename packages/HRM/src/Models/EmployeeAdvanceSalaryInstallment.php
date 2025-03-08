<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeAdvanceSalaryInstallment extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'advance_salary_id', 'deduct_on', 'deduct_amount', 'status', 'created_by', 'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function advanceSalary()
    {
        return $this->belongsTo(EmployeeAdvanceSalary::class, 'advance_salary_id', 'id');
    }
}
