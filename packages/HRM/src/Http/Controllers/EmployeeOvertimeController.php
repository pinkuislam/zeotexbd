<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeOvertime;
use Oshnisoft\HRM\Models\OvertimePolicy;

class EmployeeOvertimeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee-overtime');
        $sql = EmployeeOvertime::orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('date', 'LIKE', '%' . $request->q . '%');
            });
        }


        $employee_overtimes = $sql->get();

        return view('hrm::employee-overtime', compact('employee_overtimes'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee-overtime');
        $employees = Employee::where('status', 'Active')->get();
        $policies = OvertimePolicy::where('status', 'Active')->get();
        return view('hrm::employee-overtime', compact('employees', 'policies'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'policy_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|max:255',
            'amount' => 'required|numeric',
        ]);

        $storeData = [
            'employee_id' => $request->employee_id,
            'policy_id' => $request->policy_id,
            'date' => $request->date,
            'note' => $request->note,
            'amount' => $request->amount,
            'status' => 'Pending',
            'created_by' => Auth::user()->id,
        ];

        EmployeeOvertime::create($storeData);
        $request->session()->flash('successMessage', 'Employee Overtime was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee-overtime.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = EmployeeOvertime::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-overtime.index', qArray());
        }

        return view('hrm::employee-overtime', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = EmployeeOvertime::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-overtime.index', qArray());
        }

        $employees = Employee::where('status', 'Active')->get();
        $policies = OvertimePolicy::where('status', 'Active')->get();

        return view('hrm::employee-overtime', compact('data', 'employees', 'policies'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'policy_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
        ]);


        $data = EmployeeOvertime::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-overtime.index', qArray());
        }

        $updateData = [
            'employee_id' => $request->employee_id,
            'policy_id' => $request->policy_id,
            'date' => $request->date,
            'note' => $request->note,
            'amount' => $request->amount,
            'status' => 'Pending',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($updateData);

        $request->session()->flash('successMessage', 'Employee Overtime was successfully updated!');
        return redirect()->route('oshnisoft-hrm.employee-overtime.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = EmployeeOvertime::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('employee-overtime.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'EmployeeOvertime was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.employee-overtime.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = EmployeeOvertime::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.employee-overtime.index")->with("successMessage", "EmployeeOvertime status was successfully changed!");
    }

    public function reject(Request $request, $id)
    {
        $data = EmployeeOvertime::find($id);
        $storeData = [
            'status' => 'Canceled',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Canceled!');
        return redirect()->route('oshnisoft-hrm.employee-overtime.index');
    }

    public function approve(Request $request, $id)
    {
        $data = EmployeeOvertime::find($id);
        $storeData = [
            'status' => 'Approved',
            'approve_at' => now(),
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('oshnisoft-hrm.employee-overtime.index');
    }
}
