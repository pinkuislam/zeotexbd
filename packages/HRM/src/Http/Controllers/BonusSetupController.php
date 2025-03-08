<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\BonusSetup;
use Oshnisoft\HRM\Models\Employee;

class BonusSetupController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_bonus-setup');
        $sql = BonusSetup::orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('bonus_date', 'LIKE', '%' . $request->q . '%');
            });
        }


        $bonuses = $sql->get();

        return view('hrm::bonus-setup', compact('bonuses'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_bonus-setup');
        $employees = Employee::where('status', 'Active')->get();
        return view('hrm::bonus-setup', compact('employees'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'bonus_date' => 'required|date',
            'title' => 'required|max:255',
            'percent' => 'required|numeric',
            'percent_type' => 'required|in:Basic,Gross',
        ]);

        $storeData = [
            'bonus_date' => $request->bonus_date,
            'title' => $request->title,
            'percent_type' => $request->percent_type,
            'percent' => $request->percent,
            'status' => 'Active',
            'created_by' => Auth::user()->id,
        ];

        BonusSetup::create($storeData);
        $request->session()->flash('successMessage', 'Bonus Setup was successfully added!');
        return redirect()->route('oshnisoft-hrm.bonus-setup.create', qArray());
    }


    public function show(Request $request, $id)
    {
        $data = BonusSetup::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.bonus-setup.index', qArray());
        }

        return view('hrm::bonus-setup', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = BonusSetup::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.bonus-setup.index', qArray());
        }

        $employees = Employee::where('status', 'Active')->get();

        return view('hrm::bonus-setup', compact('data', 'employees'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bonus_date' => 'required|date',
            'title' => 'required|max:255',
            'percent' => 'required|numeric',
            'percent_type' => 'required|in:Basic,Gross',
        ]);


        $data = BonusSetup::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.bonus-setup.index', qArray());
        }

        $updateData = [
            'bonus_date' => $request->bonus_date,
            'title' => $request->title,
            'percent_type' => $request->percent_type,
            'percent' => $request->percent,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($updateData);

        $request->session()->flash('successMessage', 'Bonus Setup was successfully updated!');
        return redirect()->route('oshnisoft-hrm.bonus-setup.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = BonusSetup::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('bonus-setup.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'Bonus Setup was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.bonus-setup.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = BonusSetup::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.bonus-setup.index")->with("successMessage", "Bonus Setup status was successfully changed!");
    }

    public function reject(Request $request, $id)
    {
        $data = BonusSetup::find($id);
        $storeData = [
            'status' => 'Canceled',
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Canceled!');
        return redirect()->route('oshnisoft-hrm.bonus-setup.index');
    }
}
