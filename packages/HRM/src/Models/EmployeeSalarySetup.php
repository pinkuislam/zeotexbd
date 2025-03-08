<?php

namespace Oshnisoft\HRM\Models;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeSalarySetup extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'basic_salary', 'house_rent', 'medical_allowance', 'conveyance_allowance', 'entertainment_allowance',
        'other_allowance', 'income_tax', 'pf_deduction', 'mobile_bill', 'gross_salary', 'bank_acc_no', 'bank_id', 'created_by', 'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function salaryBank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }
}
