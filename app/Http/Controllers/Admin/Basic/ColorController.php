<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Models\Color;
use App\Exports\ColorExport;
use App\Imports\ColorImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ColorController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list color');
        $sql = Color::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $colors = $sql->get();

        return view('admin.basic.color', compact('colors'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add color');
        return view('admin.basic.color')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add color');
        $this->validate($request, [
            'name' => 'required|max:255|unique:colors,name',
            'color_code' => 'required|max:255|unique:colors,color_code',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'color_code' => $request->color_code,
            'status' => $request->status,
            'created_by' => auth()->user()->id
        ];
        Color::create($storeData);

        $request->session()->flash('successMessage', 'Color was successfully added!');
        return redirect()->route('admin.basic.color.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show color');
        $data = Color::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.colorindex', qArray());
        }
        return view('admin.basic.color', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit color');
        $data = Color::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.color.index', qArray());
        }
        return view('admin.basic.color', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit color');
        $this->validate($request, [
            'name' => 'required|max:255|unique:colors,name,'.$id.',id',
            'color_code' => 'required|max:255|unique:colors,color_code,'.$id.',id',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Color::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.color.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'color_code' => $request->color_code,
            'status' => $request->status,
            'updated_by' => auth()->user()->id
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Color was successfully updated!');
        return redirect()->route('admin.basic.color.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete color');
        $data = Color::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.colorindex', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Color was successfully deleted!');
        return redirect()->route('admin.basic.color.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status color');
        $data = Color::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("admin.basic.color.index")->with("successMessage", "Color status was successfully changed!");
    }
    public function import(Request $request)
    {
        $this->authorize('add unit');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new ColorImport, $request->file);

        $request->session()->flash('successMessage', 'Color was successfully imported!');
        return redirect()->route('admin.basic.color.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list color');

        return Excel::download(new ColorExport, 'color-'. time() . '.xlsx');
    }
}
