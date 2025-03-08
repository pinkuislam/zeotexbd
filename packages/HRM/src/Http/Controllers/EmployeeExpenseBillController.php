<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeExpenseBill;

class EmployeeExpenseBillController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee-expense-bill');
        $sql = EmployeeExpenseBill::orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('date', 'LIKE', '%' . $request->q . '%');
            });
        }
        $employee_expense_bills = $sql->get();

        return view('hrm::employee-expense-bill', compact('employee_expense_bills'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee-expense-bill');
        $employees = Employee::where('status', 'Active')->get();
        return view('hrm::employee-expense-bill', compact('employees'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'da_amount' => 'required|numeric',
            'ta_amount' => 'required|numeric',
            'hotel_bill' => 'required|numeric',
            'daily_summary' => 'nullable|string',
        ]);

        $total = $request->ta_amount + $request->da_amount + $request->hotel_bill;

        $storeData = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'da_amount' => $request->da_amount,
            'ta_amount' => $request->ta_amount,
            'hotel_bill' => $request->hotel_bill,
            'daily_summary' => $request->daily_summary,
            'total_amount' => $total,
            'status' => 'Pending',
            'created_by' => Auth::user()->id,
        ];

        EmployeeExpenseBill::create($storeData);
        $request->session()->flash('successMessage', 'Employee Expense Bill was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee-expense-bill.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = EmployeeExpenseBill::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-expense-bill.index', qArray());
        }

        return view('hrm::employee-expense-bill', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = EmployeeExpenseBill::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-expense-bill.index', qArray());
        }

        $employees = Employee::where('status', 'Active')->get();

        return view('hrm::employee-expense-bill', compact('data', 'employees'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'da_amount' => 'required|numeric',
            'ta_amount' => 'required|numeric',
            'hotel_bill' => 'required|numeric',
            'daily_summary' => 'nullable|string',
        ]);


        $data = EmployeeExpenseBill::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-expense-bill.index', qArray());
        }
        $total = $request->ta_amount + $request->da_amount + $request->hotel_bill;

        $updateData = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'da_amount' => $request->da_amount,
            'ta_amount' => $request->ta_amount,
            'hotel_bill' => $request->hotel_bill,
            'daily_summary' => $request->daily_summary,
            'total_amount' => $total,
            'status' => 'Pending',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($updateData);

        $request->session()->flash('successMessage', 'Employee Expense Bill was successfully updated!');
        return redirect()->route('oshnisoft-hrm.employee-expense-bill.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = EmployeeExpenseBill::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('employee-expense-bill.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'EmployeeExpenseBill was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.employee-expense-bill.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = EmployeeExpenseBill::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.employee-expense-bill.index")->with("successMessage", "EmployeeExpenseBill status was successfully changed!");
    }

    public function reject(Request $request, $id)
    {
        $data = EmployeeExpenseBill::find($id);
        $storeData = [
            'status' => 'Canceled',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Canceled!');
        return redirect()->route('oshnisoft-hrm.employee-expense-bill.index');
    }

    public function approve(Request $request, $id)
    {
        $data = EmployeeExpenseBill::find($id);
        $storeData = [
            'status' => 'Approved',
            'approve_at' => now(),
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('oshnisoft-hrm.employee-expense-bill.index');
    }
}
