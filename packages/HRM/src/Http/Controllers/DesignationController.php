<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\Designation;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_designation');
        $sql = Designation::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $designations = $sql->get();

        return view('hrm::designation', compact('designations'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_designation');
        return view('hrm::designation')->with('create', 1);
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
        Designation::create($storeData);

        $request->session()->flash('successMessage', 'Designation was successfully added!');
        return redirect()->route('oshnisoft-hrm.designation.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = Designation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.designation.index', qArray());
        }

        return view('hrm::designation', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = Designation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.designation.index', qArray());
        }

        return view('hrm::designation', compact('data'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Designation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.designation.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Designation was successfully updated!');
        return redirect()->route('oshnisoft-hrm.designation.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = Designation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('designation.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'Designation was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.designation.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = Designation::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.designation.index")->with("successMessage", "Designation status was successfully changed!");
    }
}
