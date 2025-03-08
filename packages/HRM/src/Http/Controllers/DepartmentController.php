<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Department;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_department');
        $sql = Department::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $departments = $sql->get();

        return view('hrm::department', compact('departments'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_department');
        return view('hrm::department')->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        Department::create($storeData);

        $request->session()->flash('successMessage', 'Department was successfully added!');
        return redirect()->route('oshnisoft-hrm.department.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = Department::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.department.index', qArray());
        }

        return view('hrm::department', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = Department::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.department.index', qArray());
        }

        return view('hrm::department', compact('data'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Department::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.department.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Department was successfully updated!');
        return redirect()->route('oshnisoft-hrm.department.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = Department::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('department.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'Department was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.department.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = Department::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.department.index")->with("successMessage", "Department status was successfully changed!");
    }
}
