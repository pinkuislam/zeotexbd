<?php

namespace Oshnisoft\HRM\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Oshnisoft\HRM\Models\Employee;

class EmployeesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $employees = Employee::select(
            'employees.employee_no',
            'employees.name',
            'departments.name AS department',
            'designations.name AS designation',
            'employees.org_joining_date',
            'employees.contact_no',
            'employees.present_address',
        //'hr_employee_salary.gross_salary',
        )
            ->leftJoin('employment_statuses', function ($join) {
                $join->on('employees.id', '=', 'employment_statuses.employee_id')
                    ->on('employment_statuses.id', '=', DB::raw("(select max(id) from employment_statuses WHERE employment_statuses.employee_id = employees.id)"));
            })
            ->join('departments', 'employment_statuses.department_id', '=', 'departments.id')
            ->join('designations', 'employment_statuses.designation_id', '=', 'designations.id')
            //->leftJoin('hr_employee_salary', 'employees.id', '=', 'hr_employee_salary.employee_id')
            ->where('employees.status', 'Active')
            ->get();

        return $employees;
    }

    public function headings(): array
    {
        return ["Staff ID", "Name", "Department", "Designation", "Joining Date", "Contact No", "Present Address"];
    }
}
