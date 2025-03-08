<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeSalaryAdjustment;

class EmployeePenaltyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee-penalty');
        $sql = EmployeeSalaryAdjustment::where('type', 'Penalty')->orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('date', 'LIKE', '%' . $request->q . '%');
            });
        }


        $employee_penalty = $sql->get();

        return view('hrm::employee-penalty', compact('employee_penalty'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee-penalty');
        $employees = Employee::where('status', 'Active')->get();
        return view('hrm::employee-penalty', compact('employees'))->with('create', 1);
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
            'type' => 'Penalty',
            'created_by' => Auth::user()->id,
        ];

        EmployeeSalaryAdjustment::create($storeData);
        $request->session()->flash('successMessage', 'Employee Penalty was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee-penalty.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-penalty.index', qArray());
        }

        return view('hrm::employee-penalty', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee-penalty.index', qArray());
        }
        
        $employees = Employee::where('status', 'Active')->get();
        
        return view('hrm::employee-penalty', compact('data', 'employees'))->with('edit', $id);
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
            return redirect()->route('oshnisoft-hrm.employee-penalty.index', qArray());
        }

        $updateData = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'note' => $request->note,
            'amount' => $request->amount,
            'status' => 'Pending',
            'type' => 'Penalty',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($updateData);

        $request->session()->flash('successMessage', 'Employee Penalty was successfully updated!');
        return redirect()->route('oshnisoft-hrm.employee-penalty.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('employee-penalty.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'EmployeePenalty was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.employee-penalty.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = EmployeeSalaryAdjustment::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.employee-penalty.index")->with("successMessage", "EmployeePenalty status was successfully changed!");
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
        return redirect()->route('oshnisoft-hrm.employee-penalty.index');
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
        return redirect()->route('oshnisoft-hrm.employee-penalty.index');
    }
}
