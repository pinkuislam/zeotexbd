<?php

namespace Oshnisoft\HRM\Exports;

use Oshnisoft\HRM\Services\HrService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalaryDraftExport implements FromArray, WithHeadings
{
    protected $year;
    protected $month;
    protected $ids;

    public function __construct($year, $month, $ids)
    {
        $this->year = $year;
        $this->month = $month;
        $this->ids = $ids;
    }

    public function array(): array
    {
        $records = HrService::salaryGenerate($this->year, $this->month, $this->ids);

        $data = [];
        foreach ($records as $val) {
            $data[] = [
                'id' => $val->employee_no,
                'name' => $val->name,
                'department_name' => $val->department_name,
                'designation_name' => $val->designation_name,
                'org_joining_date' => $val->org_joining_date,
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
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
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
            'Penalty (-)',
            'Salary Advance (-)',
            'Provident Fund (-)',
            'Income Tax  (-)',
            'Net Salary'
        ];
    }
}
