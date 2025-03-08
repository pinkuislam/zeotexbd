<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeAdvanceSalary;
use Oshnisoft\HRM\Models\EmployeeAdvanceSalaryInstallment;

class EmployeeAdvanceSalaryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee-advance-salary');
        $sql = EmployeeAdvanceSalary::orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('date', 'LIKE', '%' . $request->q . '%');
            });
        }


        $employee_advance_salaries = $sql->get();

        return view('hrm::employee-advance-salary', compact('employee_advance_salaries'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee-advance-salary');
        $employees = Employee::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();
        return view('hrm::employee-advance-salary', compact('employees', 'banks'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
            'deduct_type' => 'required|in:onetime,installment',
            'deduct_start_on' => 'required|date',
        ]);

        $installmentCount = 1;

        if ($request->installment_count) {
            $installmentCount = $request->installment_count;
        }

        $storeData = [
            'employee_id' => $request->employee_id,
            'bank_id' => $request->bank_id,
            'date' => $request->date,
            'note' => $request->note,
            'amount' => $request->amount,
            'deduct_type' => $request->deduct_type,
            'deduct_start_on' => $request->deduct_start_on,
            'installment_count' => $installmentCount,
            'created_by' => Auth::user()->id,
        ];
        $data = EmployeeAdvanceSalary::create($storeData);
        //TODO:: Add Transaction
        if ($data) {
            Transaction::create([
                'type' => 'Payment',
                'flag' => 'AdvanceSalary',
                'flagable_id' => $data->id,
                'flagable_type' => EmployeeAdvanceSalary::class,
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
                'created_by' => Auth::user()->id,
            ]);
        }
        $installmentAmount = $data->amount / $installmentCount;
        $installment = [];
        $deductOn = $data->deduct_start_on;
        for ($i = 1; $i <= $installmentCount; $i++) {
            $installment[] = [
                'employee_id' => $data->employee_id,
                'advance_salary_id' => $data->id,
                'deduct_amount' => $installmentAmount,
                'status' => 'Pending',
                'deduct_on' => $deductOn,
                'created_by' => Auth::user()->id,
            ];
            $deductOn = date('Y-m-d', strtotime("+1 months", strtotime($deductOn)));
        }

        EmployeeAdvanceSalaryInstallment::insert($installment);
        $request->session()->flash('successMessage', 'Employee Advance Salary was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee-advance-salary.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = EmployeeAdvanceSalary::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-advance-salary.index', qArray());
        }

        $employees = Employee::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('hrm::employee-advance-salary', compact('data'))->with('show', $id);
    }
}
