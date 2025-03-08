<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Invest;
use App\Models\Investor;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvestController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list invest');
      
        $sql = Invest::with('bank','investor')->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('note', 'LIKE', $request->q . '%')
                    ->orWhere('amount', 'LIKE', $request->q . '%')
                    ->orWhereHas('creator', function ($query) use ($request) {
                        $query->where('name', 'LIKE', '%' . $request->q . '%');
                    });
            });
        }

        if ($request->investor) {
            $sql->where('investor_id', $request->investor);
        }
        if ($request->bank) {
            $sql->where('bank_id', $request->bank);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $invests = $sql->paginate($request->limit ?? 15);

        $banks = Bank::where('status', 'Active')->get();
        $investors = Investor::where('status', 'Active')->get();

        return view('admin.payment.invest.invest', compact('invests', 'banks', 'investors'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add invest');

        $banks = Bank::where('status', 'Active')->get();
        $investors = Investor::where('status', 'Active')->get();

        return view('admin.payment.invest.invest', compact('banks', 'investors'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add invest');

        $this->validate($request, [
            'investor_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);
        
        $storeData = [
            'investor_id' => $request->investor_id,
            'bank_id' => $request->bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
            'created_by' => Auth::user()->id,
        ];
        
        $data = Invest::create($storeData);

        if ($data) {
            Transaction::create([
                'type' => 'Received',
                'flag' => 'Invest',
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Invest',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        session()->flash('successMessage', 'Invest was successfully added!');
        return redirect()->route('admin.payment.invest.create', qArray());
    }

    public function show($id)
    {
        $this->authorize('show invest');

        $data = Invest::with(['bank','investor'])->find($id);
        if (empty($data)) {
            session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.invest.index', qArray());
        }

        return view('admin.payment.invest.invest', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit invest');

        $data = Invest::find($id);

        if (empty($data)) {
           session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.invest.index', qArray());
        }

        $banks = Bank::where('status', 'Active')->get();
        $investors = Investor::where('status', 'Active')->get();

        return view('admin.payment.invest.invest', compact('data', 'banks','investors'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit invest');

        $this->validate($request, [
            'investor_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = Invest::find($id);
        if (empty($data)) {
            session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.invest.index', qArray());
        }

        $storeData = [
            'investor_id' => $request->investor_id,
            'bank_id' => $request->bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        if ($data) {
            Transaction::updateOrCreate([
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Invest',
            ], [
                'type' => 'Received',
                'flag' => 'Invest',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $request->note,
                'amount' => $data->amount,
            ]);
        }

        session()->flash('successMessage', 'Invest was successfully updated!');
        return redirect()->route('admin.payment.invest.index', qArray());
    }

    public function destroy($id)
    {
        $this->authorize('delete invest');

        $data = Invest::find($id);
        if (empty($data)) {
            session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.invest.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\Invest')->delete();
        $data->delete();
        
        session()->flash('successMessage', 'Invest was successfully deleted!');
        return redirect()->route('admin.payment.invest.index', qArray());
    }
}
