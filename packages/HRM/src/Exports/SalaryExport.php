<?php

namespace Oshnisoft\HRM\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Oshnisoft\HRM\Models\Salary;

class SalaryExport implements FromArray, WithHeadings
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function array(): array
    {
        $records = Salary::with(['createdBy', 'updatedBy', 'employee.employmentStatus' => function ($q) {
            $q->with(['department', 'designation']);
        }])
            ->whereIn('id', $this->ids)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [];
        foreach ($records as $val) {
            $data[] = [
                'month' => monthFormat($val->salary_date),
                'id' => $val->employee->employee_no ?? '-',
                'name' => $val->employee->name ?? '-',
                'department_name' => $val->employee->employmentStatus->department->name ?? '-',
                'designation_name' => $val->employee->employmentStatus->designation->name ?? '-',
                'org_joining_date' => $val->employee->org_joining_date ?? '-',
                'gross_salary' => $val->gross_salary,
                'mobile_bill' => $val->mobile_bill,
                'overtime_amount' => $val->overtime_amount,
                'bonus_amount' => $val->bonus_amount,
                'expense_amount' => $val->expense_amount,
                'consider_amount' => $val->consider_amount,
                'incentive_amount' => $val->incentive_amount,
                'penalty_amount' => $val->penalty_amount,
                'advance_amount' => $val->advance_amount,
                'pf_deduction' => $val->pf_deduction,
                'income_tax' => $val->income_tax,
                'net_salary' => $val->net_salary,
                'createdBy' => $val->createdBy->name ?? '-',
                'updatedBy' => $val->updatedBy->name ?? '-',
                'paid_at' => $val->paid_at,
                'status' => $val->status,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Month',
            'ID',
            'Name',
            'Department',
            'Designation',
            'Joining Date',
            'Gross Salary',
            'Phone Bill',
            'Overtime',
            'Bonus',
            'Expense Bill',
            'Consideration',
            'Incentive',
            'Penalty',
            'Salary Advance',
            'Provident Fund',
            'Income Tax',
            'Net Salary',
            'Generate By',
            'Paid By',
            'Paid At',
            'Status'
        ];
    }
}
