<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Oshnisoft\HRM\Services\HrService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Oshnisoft\HRM\Exports\SalaryDraftExport;
use Oshnisoft\HRM\Exports\SalaryExport;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\Salary;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_salary');

        $sql = Salary::with(['employee.employmentStatus' => function ($q) {
            $q->with(['department', 'designation']);
        }])->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('employee_no', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('contact_no', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->employee) {
            $sql->where('employee_id', $request->employee);
        }

        if ($request->year) {
            $sql->where('year', $request->year);
        }

        if ($request->month) {
            $sql->where('month', $request->month);
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->get();
        $employees = Employee::select('id', 'employee_no', 'name')->where('status', 'Active')->get();

        return view('hrm::salary.index', compact('records', 'employees'));
    }

    public function create(Request $request)
    {
        $this->authorize('add hr_salary');

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $records = HrService::salaryGenerate($year, $month);
        return view('hrm::salary.create', compact('year', 'month', 'records'));
    }

    public function store(Request $request)
    {
        $this->authorize('add hr_salary');

        $validator = Validator::make($request->except('_token'), [
            'year' => 'required|max:4|min:4',
            'month' => 'required|max:2|min:2',
            'ids' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('errorMessage', implode("<br>", $validator->messages()->all()));
            return redirect()->back();
        }

        if ($request->action == 'export') {
            return Excel::download(new SalaryDraftExport($request->year, $request->month, $request->ids), 'draft-salary-' . time() . '.xlsx');
        } else {
            try {
                $records = HrService::salaryGenerate($request->year, $request->month, $request->ids);

                foreach ($records as $val) {
                    $salary = [
                        'employee_id' => $val->id,
                        'year' => $request->year,
                        'month' => $request->month,
                        'salary_date' => $request->year . '-' . $request->month . '-01',
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
                        'status' => 'Processed',
                        'created_by' => Auth::user()->id,
                        'created_at' => now(),
                    ];
                    Salary::create($salary);
                }


            } catch (Exception $e) {
                return redirect()->route("oshnisoft-hrm.salaries.create")->with("errorMessage", "Salary Generate Failed for: " . $e->getMessage());
            }

            $request->session()->flash('successMessage', 'Salary Generate successfully.');
            return redirect()->route('oshnisoft-hrm.salaries.create', qArray());
        }
    }

    public function payment(Request $request)
    {
        $this->authorize('payment hr_salary');

        $validator = Validator::make($request->except('_token'), [
            'ids' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('errorMessage', implode("<br>", $validator->messages()->all()));
            return redirect()->back();
        }

        if ($request->action == 'export') {
            return Excel::download(new SalaryExport($request->ids), 'salary-' . time() . '.xlsx');
        } else {
            try {
                DB::beginTransaction();

                $salaries = Salary::with('employee.salary')->select('id', 'employee_id', 'net_salary')->whereIn('id', $request->ids)->get();
                foreach ($salaries as $val) {
                    if ($val->employee != null && $val->employee->salary != null) {
                        $transaction = Transaction::create([
                            'type' => 'Payment',
                            'flag' => 'Salary',
                            'flagable_id' => $val->id,
                            'flagable_type' => Salary::class,
                            'bank_id' => $val->employee->salary->bank_id,
                            'datetime' => now(),
                            'note' => 'Salary for ' . $val->employee->name . ' on Month ' . $val->month,
                            'amount' => $val->net_salary,
                            'created_by' => Auth::user()->id,
                        ]);

                        if ($transaction) {
                            $val->update(['status' => 'Paid', 'paid_at' => now(), 'updated_by' => Auth::user()->id]);
                        }
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->route("oshnisoft-hrm.salaries.index", qArray())->with("errorMessage", "Salary Payment Failed for: " . $e->getMessage());
            }

            $request->session()->flash('successMessage', 'Salary Payment successfully.');
            return redirect()->route('oshnisoft-hrm.salaries.index', qArray());
        }
    }
}
