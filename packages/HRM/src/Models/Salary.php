<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class Salary extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'salary_date',
        'gross_salary',
        'mobile_bill',
        'overtime_amount',
        'bonus_amount',
        'advance_amount',
        'consider_amount',
        'incentive_amount',
        'penalty_amount',
        'pf_deduction',
        'expense_amount',
        'income_tax',
        'net_salary',
        'status',
        'created_by',
        'updated_by',
        'paid_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
