<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Models\Unit;
use App\Exports\UnitExport;
use App\Imports\UnitImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list unit');
        $sql = Unit::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $units = $sql->get();

        return view('admin.basic.unit', compact('units'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add unit');
        return view('admin.basic.unit')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add unit');
        $this->validate($request, [
            'name' => 'required|max:255|unique:units,name',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => auth()->user()->id
        ];
        Unit::create($storeData);

        $request->session()->flash('successMessage', 'Unit was successfully added!');
        return redirect()->route('admin.basic.unit.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show unit');
        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.unitindex', qArray());
        }
        return view('admin.basic.unit', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit unit');
        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.unit.index', qArray());
        }
        return view('admin.basic.unit', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit unit');
        $this->validate($request, [
            'name' => 'required|max:255|unique:units,name,'.$id.',id',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.unit.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Unit was successfully updated!');
        return redirect()->route('admin.basic.unit.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete unit');
        $data = Unit::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.unitindex', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Unit was successfully deleted!');
        return redirect()->route('admin.basic.unit.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status unit');
        $data = Unit::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("admin.basic.unit.index")->with("successMessage", "Unit status was successfully changed!");
    }

    public function import(Request $request)
    {
        $this->authorize('add unit');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new UnitImport, $request->file);

        $request->session()->flash('successMessage', 'Unit was successfully imported!');
        return redirect()->route('admin.basic.unit.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list unit');

        return Excel::download(new UnitExport, 'unit-'. time() . '.xlsx');
    }
}