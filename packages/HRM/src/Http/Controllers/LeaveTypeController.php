<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_leave-type');

        $sql = LeaveType::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {

                $q->Where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $leave_types = $sql->get();

        return view('hrm::leave-type', compact('leave_types'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_leave-type');
        return view('hrm::leave-type')->with('create', 1);
    }


    public function store(Request $request)
    {
        //'day_count', 'remarks',
        $this->validate($request, [
            'name' => 'required|max:255',
            'day_count' => 'required|integer|min:1',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'day_count' => $request->day_count,
            'remarks' => $request->remarks,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        LeaveType::create($storeData);

        $request->session()->flash('successMessage', 'LeaveType was successfully added!');
        return redirect()->route('oshnisoft-hrm.leave-type.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = LeaveType::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.leave-type.index', qArray());
        }

        return view('hrm::leave-type', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = LeaveType::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.leave-type.index', qArray());
        }

        return view('hrm::leave-type', compact('data'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'day_count' => 'required|integer|min:1',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = LeaveType::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.leave-type.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'day_count' => $request->day_count,
            'remarks' => $request->remarks,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'LeaveType was successfully updated!');
        return redirect()->route('oshnisoft-hrm.leave-type.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = LeaveType::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('leave-type.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'LeaveType was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.leave-type.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = LeaveType::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.leave-type.index")->with("successMessage", "LeaveType status was successfully changed!");
    }
}
