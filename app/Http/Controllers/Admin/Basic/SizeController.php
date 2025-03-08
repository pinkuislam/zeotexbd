<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Exports\SizeExport;
use App\Imports\SizeImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Support\Facades\Auth;

class SizeController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list size');
        $sql = Size::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $sizes = $sql->get();

        return view('admin.basic.size', compact('sizes'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add size');
        return view('admin.basic.size')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add size');
        $this->validate($request, [
            'name' => 'required|max:255|unique:sizes,name',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => auth()->user()->id
        ];
        Size::create($storeData);

        $request->session()->flash('successMessage', 'Size was successfully added!');
        return redirect()->route('admin.basic.size.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show size');
        $data = Size::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.size.index', qArray());
        }
        return view('admin.basic.size', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit size');
        $data = Size::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.size.index', qArray());
        }
        return view('admin.basic.size', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit size');
        $this->validate($request, [
            'name' => 'required|max:255|unique:sizes,name,'.$id.',id',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Size::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.size.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Size was successfully updated!');
        return redirect()->route('admin.basic.size.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete size');
        $data = Size::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.size.index', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Size was successfully deleted!');
        return redirect()->route('admin.basic.size.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status size');
        $data = Size::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("admin.basic.size.index")->with("successMessage", "Size status was successfully changed!");
    }

    public function import(Request $request)
    {
        $this->authorize('add size');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new SizeImport, $request->file);

        $request->session()->flash('successMessage', 'Size was successfully imported!');
        return redirect()->route('admin.basic.size.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list size');

        return Excel::download(new SizeExport, 'Size-'. time() . '.xlsx');
    }
}