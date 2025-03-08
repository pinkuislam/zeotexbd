<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeSalaryAdjustment;

class EmployeeConsiderationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee-consideration');
        $sql = EmployeeSalaryAdjustment::where('type', 'Consider')->orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('date', 'LIKE', '%' . $request->q . '%');
            });
        }


        $employee_consideration = $sql->get();

        return view('hrm::employee-consideration', compact('employee_consideration'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee-consideration');
        $employees = Employee::where('status', 'Active')->get();
        return view('hrm::employee-consideration', compact('employees'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|max:255',
            'amount' => 'required|numeric',
        ]);

        $storeData = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'note' => $request->note,
            'amount' => $request->amount,
            'status' => 'Pending',
            'type' => 'Consider',
            'created_by' => Auth::user()->id,
        ];

        EmployeeSalaryAdjustment::create($storeData);
        $request->session()->flash('successMessage', 'Employee Consideration was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee-consideration.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-consideration.index', qArray());
        }

        return view('hrm::employee-consideration', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-consideration.index', qArray());
        }

        $employees = Employee::where('status', 'Active')->get();

        return view('hrm::employee-consideration', compact('data', 'employees'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|max:255',
            'amount' => 'required|numeric',
        ]);


        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-consideration.index', qArray());
        }

        $updateData = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'note' => $request->note,
            'amount' => $request->amount,
            'status' => 'Pending',
            'type' => 'Consider',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($updateData);

        $request->session()->flash('successMessage', 'Employee Consideration was successfully updated!');
        return redirect()->route('oshnisoft-hrm.employee-consideration.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('employee-consideration.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'Employee Consideration was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.employee-consideration.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.employee-consideration.index")->with("successMessage", "Employee Consideration status was successfully changed!");
    }

    public function reject(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        $storeData = [
            'status' => 'Canceled',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Canceled!');
        return redirect()->route('oshnisoft-hrm.employee-consideration.index');
    }

    public function approve(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        $storeData = [
            'status' => 'Approved',
            'approve_at' => now(),
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('oshnisoft-hrm.employee-consideration.index');
    }
}
