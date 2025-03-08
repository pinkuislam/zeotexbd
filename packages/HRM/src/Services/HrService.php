<?php

namespace Oshnisoft\HRM\Services;

use Illuminate\Support\Facades\DB;
use Oshnisoft\HRM\Models\BonusSetup;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeAdvanceSalaryInstallment;
use Oshnisoft\HRM\Models\EmployeeExpenseBill;
use Oshnisoft\HRM\Models\EmployeeOvertime;
use Oshnisoft\HRM\Models\EmployeeSalaryAdjustment;

class HrService
{
    public static function salaryGenerateQuery($year, $month, $empIdArr = [])
    {
        $startDate = $year . '-' . $month . '-' .date('d');

        $sql = Employee::select([
            'employees.id',
            'employees.employee_no',
            'employees.name',
            'employees.org_joining_date',
            'departments.name AS department_name',
            'designations.name AS designation_name',
            'employee_salary_setups.basic_salary',
            'employee_salary_setups.gross_salary',
            'employee_salary_setups.mobile_bill',
            'employee_salary_setups.income_tax',
            'employee_salary_setups.pf_deduction',
            'overtimes.overtime_amount',
            'expenses.expense_amount',
            'considers.consider_amount',
            'incentives.incentive_amount',
            'penalties.penalty_amount',
            'advances.advance_amount',
        ])
            ->join('employment_statuses', function ($q) use ($startDate) {
                $q->on('employment_statuses.employee_id', '=', 'employees.id');
                $q->where('effect_date', '<=', $startDate);
            })
            ->leftJoin('departments', function ($q) {
                $q->on('employment_statuses.department_id', '=', 'departments.id');
            })
            ->leftJoin('designations', function ($q) {
                $q->on('employment_statuses.designation_id', '=', 'designations.id');
            })
            ->join('employee_salary_setups', function ($q) {
                $q->on('employee_salary_setups.employee_id', '=', 'employees.id');
            })
            ->leftJoinSub(
                EmployeeOvertime::select(['employee_id', DB::raw('SUM(amount) AS overtime_amount')])
                    ->where('status', 'Approved')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->groupBy('employee_id')
                , 'overtimes', function ($q) {
                $q->on('overtimes.employee_id', '=', 'employees.id');
            })
            ->leftJoinSub(
                EmployeeExpenseBill::select(['employee_id', DB::raw('SUM(total_amount) AS expense_amount')])
                    ->where('status', 'Approved')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->groupBy('employee_id'),
                'expenses', 'expenses.employee_id', '=', 'employees.id'
            )
            ->leftJoinSub(
                EmployeeSalaryAdjustment::select(['employee_id', DB::raw('SUM(amount) AS consider_amount')])
                    ->where('type', 'Consider')
                    ->where('status', 'Approved')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->groupBy('employee_id'),
                'considers', 'considers.employee_id', '=', 'employees.id'
            )
            ->leftJoinSub(
                EmployeeSalaryAdjustment::select(['employee_id', DB::raw('SUM(amount) AS incentive_amount')])
                    ->where('type', 'Incentive')
                    ->where('status', 'Approved')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->groupBy('employee_id'),
                'incentives', 'incentives.employee_id', '=', 'employees.id'
            )
            ->leftJoinSub(
                EmployeeSalaryAdjustment::select(['employee_id', DB::raw('SUM(amount) AS penalty_amount')])
                    ->where('type', 'Penalty')
                    ->where('status', 'Approved')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->groupBy('employee_id'),
                'penalties', 'penalties.employee_id', '=', 'employees.id'
            )
            ->leftJoinSub(
                EmployeeAdvanceSalaryInstallment::select(['employee_id', DB::raw('SUM(deduct_amount) AS advance_amount')])
                    ->where('status', 'Processed')
                    ->whereYear('deduct_on', $year)
                    ->whereMonth('deduct_on', $month)
                    ->groupBy('employee_id'),
                'advances', 'advances.employee_id', '=', 'employees.id'
            )
            ->where('employees.status', 'Active');

        if (!empty($empIdArr)) {
            $sql->whereIn('employees.id', $empIdArr);
        }

        $sql->leftJoin('salaries', function ($q) use ($year, $month) {
            $q->on('salaries.employee_id', '=', 'employees.id');
            $q->where('year', $year);
            $q->where('month', $month);
        })
            ->whereNull('salaries.id');
        return $sql;
    }

    public static function salaryGenerate($year, $month, $empIdArr = [])
    {
        $bonus = BonusSetup::select('percent_type', 'percent')
            ->where(DB::raw('YEAR(bonus_date)'), $year)
            ->where(DB::raw('MONTH(bonus_date)'), $month)
            ->first();

        $records = self::salaryGenerateQuery($year, $month, $empIdArr)->get();
        $records->map(function ($val) use ($bonus, $year, $month) {

            $bonus_amount = 0;
            if ($bonus) {
                if ($bonus->percent_type == 'Basic') {
                    $bonus_amount = numberFormat(($val->basic_salary * $bonus->percent) / 100);
                } elseif ($bonus->percent_type == 'Gross') {
                    $bonus_amount = numberFormat(($val->gross_salary * $bonus->percent) / 100);
                }
            }
            $val->bonus_amount = $bonus_amount;

            $net_salary = (($val->gross_salary + $val->mobile_bill + $val->overtime_amount + $bonus_amount + $val->expense_amount + $val->consider_amount + $val->incentive_amount) - ($val->penalty_amount + $val->advance_amount + $val->pf_deduction + $val->income_tax));

            $dayWiseSalary = 0;
            if ($val->org_joining_date > $year . '-' . $month . '-01') {
                $joinArr = explode('-', $val->org_joining_date);
                $monthDays = monthDays($year, $month);
                $workingDays = ($monthDays - $joinArr[2]);
                $perDaySalary = ($net_salary / $monthDays);
                $dayWiseSalary = $workingDays * $perDaySalary;
            }

            if ($dayWiseSalary > 0) {
                $val->net_salary = numberFormat($dayWiseSalary);
            } else {
                $val->net_salary = numberFormat($net_salary);
            }

            return $val;
        });

        return $records;
    }
}
