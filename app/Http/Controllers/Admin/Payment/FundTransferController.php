<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Transaction;
use App\Models\FundTransfer;
use Illuminate\Http\Request;
use App\Services\BankService;
use App\Services\CodeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FundTransferController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list fund-transfer');
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            $sql = FundTransfer::with('fromBank', 'toBank')->orderBy('id', 'DESC');
        }else{
            $sql = FundTransfer::with('fromBank', 'toBank')->where('created_by', auth()->user()->id)->orWhere(function($q){
                $q->whereIn('to_bank_id', BankService::userBank(auth()->user()));
            })->orderBy('id', 'DESC');
        }

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('transfer_no', 'LIKE', $request->q.'%')
                    ->orWhere('note', 'LIKE', $request->q.'%');
            });
        }

        if ($request->bank) {
            $sql->where(function($q) use($request) {
                $q->where('from_bank_id', $request->bank);
                $q->orWhere('to_bank_id', $request->bank);
            });
        }

        if ($request->status) {
            $sql->where('approved_at', $request->status == 'Approved' ? '!=' : '=' , NULL );
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $records = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        $banks = BankService::allBank(Auth::user());

        return view('admin.payment.fund-transfer.index', compact('records', 'banks'));
    }

    public function create()
    {
        $this->authorize('add fund-transfer');
        $banks = BankService::allBank(Auth::user());
        return view('admin.payment.fund-transfer.create', compact('banks'));
    }

    public function store(Request $request)
    {
        $this->authorize('add fund-transfer');

        $this->validate($request, [
            'from_bank_id' => 'required|integer',
            'to_bank_id' => 'required|integer|different:from_bank_id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        if (!$this->checkBalance($request)) {
            $request->session()->flash('errorMessage', 'Bank Balance amount not exist.');
            return redirect()->route('admin.payment.fund-transfers.create', qArray());
        }

        $transferNo = CodeService::generate(FundTransfer::class, 'FT', 'transfer_no');

        $storeData = [
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id' => $request->to_bank_id,
            'transfer_no' => $transferNo,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
            'created_by' => Auth::user()->id,
        ];
        $data = FundTransfer::create($storeData);
        if ($data) {
            Transaction::insert([
                [
                    'type' => 'Payment',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => FundTransfer::class,
                    'bank_id' => $data->from_bank_id,
                    'datetime' => dbDateFormat($request->date),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => $data->date,
                    'created_by' => Auth::user()->id,
                ],
                [
                    'type' => 'Received',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => FundTransfer::class,
                    'bank_id' => $data->to_bank_id,
                    'datetime' => dbDateFormat($request->date),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => $data->date,
                    'created_by' => Auth::user()->id,
                ]
            ]);
        }

        $request->session()->put('payDate', $request->date);

        $request->session()->flash('successMessage', 'Fund Transfer was successfully added!');
        return redirect()->route('admin.payment.fund-transfers.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show fund-transfer');

        $data = FundTransfer::with('fromBank', 'toBank')->findOrFail($id);
        return view('admin.payment.fund-transfer.show', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit fund-transfer');

        $data = FundTransfer::findOrFail($id);

        $banks = BankService::allBank(Auth::user());

        return view('admin.payment.fund-transfer.edit', compact('data', 'banks'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit fund-transfer');

        $this->validate($request, [
            'from_bank_id' => 'required|integer',
            'to_bank_id' => 'required|integer|different:from_bank_id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = FundTransfer::findOrFail($id);

        if (!$this->checkBalance($request, $id)) {
            $request->session()->flash('errorMessage', 'Bank Balance amount not exist.');
            return redirect()->route('admin.payment.fund-transfers.edit', $id);
        }

        $storeData = [
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id' => $request->to_bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        if ($data) {
            Transaction::where('flagable_id', $data->id)->where('flagable_type', FundTransfer::class)->forceDelete();
            Transaction::insert([
                [
                    'type' => 'Payment',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => FundTransfer::class,
                    'bank_id' => $data->from_bank_id,
                    'datetime' => dbDateFormat($request->date),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => $data->date,
                    'created_by' => Auth::user()->id,
                    'updated_at' => $data->date,
                    'updated_by' => Auth::user()->id,
                ],
                [
                    'type' => 'Received',
                    'flag' => 'Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => FundTransfer::class,
                    'bank_id' => $data->to_bank_id,
                    'datetime' => dbDateFormat($request->date),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => $data->date,
                    'created_by' => Auth::user()->id,
                    'updated_at' => $data->date,
                    'updated_by' => Auth::user()->id,
                ]
            ]);
        }
        $request->session()->put('payDate', $request->date);

        $request->session()->flash('successMessage', 'Fund Transfer was successfully updated!');
        return redirect()->route('admin.payment.fund-transfers.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete fund-transfer');

        $data = FundTransfer::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.fund-transfers.index', qArray());
        }

        Transaction::where('flagable_id', $data->id)->where('flagable_type', FundTransfer::class)->delete();
        $data->delete();
        
        $request->session()->flash('successMessage', 'Fund Transfer was successfully deleted!');
        return redirect()->route('admin.payment.fund-transfers.index', qArray());
    }

    private function checkBalance(Request $request, $editId = null)
    {
        $receive = Transaction::where('type', 'Received')->where('bank_id', $request->from_bank_id)->sum('amount');
        $issue = Transaction::where('type', 'Payment')->where('bank_id', $request->from_bank_id)->sum('amount');

        $balance = ($receive-$issue);
        if ($balance >= $request->amount) {
            return true;
        }
        return false;
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('approval fund-transfer');
        
        $data = FundTransfer::findOrFail($id);

        $approvedData = [
            'approved_at' => now(),
            'updated_by' => Auth::user()->id,
            'approved_by' => Auth::user()->id,
        ];

        $data->update($approvedData);
        $data->transactions()->update($approvedData);

        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('admin.payment.fund-transfers.index');
    }
}

