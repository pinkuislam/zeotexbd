<?php

namespace Oshnisoft\HRM\Models;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeAdvanceSalary extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'bank_id', 'date', 'note', 'amount', 'deduct_type', 'installment_count', 'deduct_start_on', 'created_by', 'updated_by'
    ];

    public function installments()
    {
        return $this->hasMany(EmployeeAdvanceSalaryInstallment::class, 'advance_salary_id', 'id');
    }

    public function deductAmount()
    {
        return $this->hasMany(EmployeeAdvanceSalaryInstallment::class, 'advance_salary_id', 'id')->where('status', 'Processed');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }
}
