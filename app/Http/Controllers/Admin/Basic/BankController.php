<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\CodeService;

class BankController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list bank');

        $sql = Bank::orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('account_name', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('account_no', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('bank_name', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('branch_name', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('opening_balance', 'LIKE', '%'. $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $banks = $sql->paginate($request->per_page_limit ?? config('settings.per_page_limit'));
        
        return view('admin.basic.bank', compact('banks'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add bank');
        return view('admin.basic.bank')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add bank');

        $this->validate($request, [
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255|unique:banks,bank_name,id',
            'branch_name' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);
        
        $code = CodeService::generate(Bank::class, 'B', 'code');
        $storeData = [
            'code' => $code,
            'account_name' => $request->account_name,
            'account_no' => $request->account_no,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'opening_balance' => $request->opening_balance,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ];
        Bank::create($storeData);

        $request->session()->flash('successMessage', 'Bank was successfully added!');
        return redirect()->route('admin.basic.bank.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show bank');
        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.bank.index', qArray());
        }

        return view('admin.basic.bank', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit bank');
        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.bank.index', qArray());
        }

        return view('admin.basic.bank', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit bank');

        $this->validate($request, [
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255|unique:banks,bank_name,'.$id.',id',
            'branch_name' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric',
            'status' => 'required|in:Active,Deactivated',
        ]);
        

        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.bank.index', qArray());
        }

        $storeData = [
            'account_name' => $request->account_name,
            'account_no' => $request->account_no,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'opening_balance' => $request->opening_balance,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Bank was successfully updated!');
        return redirect()->route('admin.basic.bank.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete bank');
        $data = Bank::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.bank.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'Bank was successfully deleted!');
        return redirect()->route('admin.basic.bank.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status bank');

        $data = Bank::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);

        return redirect()->route("admin.basic.bank.index")->with("successMessage", "Bank status was successfully changed!");
    }
    public function due(Request $request)
    {
        $data = Bank::find($request->id);
        $receivedAmount = Transaction::where('type', 'Received')->where('bank_id', $request->id)->sum('amount');
        $paymentAmount = Transaction::where('type', 'Payment')->where('bank_id', $request->id)->sum('amount');
        $due = ($data->opening_balance + $receivedAmount - $paymentAmount);
        return response()->json(['success' => true, 'due' => $due]);
    }
}