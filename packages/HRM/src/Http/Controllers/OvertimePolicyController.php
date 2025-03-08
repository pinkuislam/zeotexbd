<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\OvertimePolicy;

class OvertimePolicyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_overtime-policy');

        $sql = OvertimePolicy::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {

                $q->Where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $overtime_policies = $sql->get();

        return view('hrm::overtime-policy', compact('overtime_policies'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_overtime-policy');
        return view('hrm::overtime-policy')->with('create', 1);
    }


    public function store(Request $request)
    {
        //'day_count', 'remarks',
        $this->validate($request, [
            'name' => 'required|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'amount' => $request->amount,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        OvertimePolicy::create($storeData);

        $request->session()->flash('successMessage', 'OvertimePolicy was successfully added!');
        return redirect()->route('oshnisoft-hrm.overtime-policy.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = OvertimePolicy::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.overtime-policy.index', qArray());
        }

        return view('hrm::overtime-policy', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = OvertimePolicy::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.overtime-policy.index', qArray());
        }

        return view('hrm::overtime-policy', compact('data'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = OvertimePolicy::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.overtime-policy.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'amount' => $request->amount,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'OvertimePolicy was successfully updated!');
        return redirect()->route('oshnisoft-hrm.overtime-policy.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = OvertimePolicy::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('overtime-policy.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'OvertimePolicy was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.overtime-policy.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = OvertimePolicy::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.overtime-policy.index")->with("successMessage", "OvertimePolicy status was successfully changed!");
    }
}
