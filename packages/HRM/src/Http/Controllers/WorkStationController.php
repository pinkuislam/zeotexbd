<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\WorkStation;

class WorkStationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_work-station');

        $sql = WorkStation::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {

                $q->Where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('address', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $work_stations = $sql->get();

        return view('hrm::work-station', compact('work_stations'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_work-station');
        return view('hrm::work-station')->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'address' => 'required',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'address' => $request->address,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        WorkStation::create($storeData);

        $request->session()->flash('successMessage', 'WorkStation was successfully added!');
        return redirect()->route('oshnisoft-hrm.work-station.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = WorkStation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.work-station.index', qArray());
        }

        return view('hrm::work-station', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = WorkStation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.work-station.index', qArray());
        }

        return view('hrm::work-station', compact('data'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'address' => 'required',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = WorkStation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.work-station.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'address' => $request->address,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'WorkStation was successfully updated!');
        return redirect()->route('oshnisoft-hrm.work-station.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = WorkStation::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('work-station.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'WorkStation was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.work-station.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = WorkStation::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.work-station.index")->with("successMessage", "WorkStation status was successfully changed!");
    }
}
