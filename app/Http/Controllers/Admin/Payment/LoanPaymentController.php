<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Loan;
use App\Models\LoanHolder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoanPaymentController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('list loan');

        $sql = Loan::with(['loanHolder']);

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('serial_number', 'LIKE', $request->q . '%')
                    ->orWhere('note', 'LIKE', $request->q . '%');
            });
        }

        if ($request->bank) {
            $sql->whereHas('transactions', function ($q) use ($request) {
                $q->where('bank_id', $request->bank);
            });
        }

        if ($request->loanHolder) {
            $sql->where('loan_holder_id', $request->loanHolder);
        }

        if ($request->from) {
            $sql->where('date', '>=', $request->from);
        }

        if ($request->to) {
            $sql->where('date', '<=', $request->to);
        }

        $records = $sql->orderBy('id', 'DESC')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        $banks = Bank::select(['id', 'bank_name'])->where('status', 'Active')->get();
        $loanHolders = LoanHolder::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.loan-payment.index', compact('serial', 'records', 'banks', 'loanHolders'));
    }

    public function create()
    {
        $this->authorize('add loan');

        $items = [
            (object) [
                'id' => 0,
                'bank_id' => null,
                'amount' => null,
            ]
        ];

        $banks = Bank::select(['id', 'bank_name'])->where('status', 'Active')->get();
        $loanHolders = LoanHolder::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.loan-payment.create', compact('banks', 'loanHolders', 'items'));
    }

    public function adjustment()
    {
        $this->authorize('add loan');

        $banks = Bank::select(['id', 'bank_name'])->where('status', 'Active')->get();
        $loanHolders = LoanHolder::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();
        $adjustment = true;
        

        return view('admin.payment.loan-payment.create', compact('banks', 'loanHolders', 'adjustment'));
    }

    public function store(Request $request)
    {
        $this->authorize('add loan');

        $this->validate($request, [
            'loan_holder_id' => 'required|integer',
            'type' => 'required|in:Received,Payment,Adjustment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);
        $storeData = [
            'loan_holder_id' => $request->loan_holder_id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->total_amount,
            'note' => $request->note,
            'created_by' => Auth::user()->id,
        ];
        $data = Loan::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            foreach ($request->transaction_id as $key => $tranId) {
                $transactionData = [
                    'type' => $data->type,
                    'flag' => 'Loan',
                    'flagable_id' => $data->id,
                    'flagable_type' => Loan::class,
                    'bank_id' => $request->bank_id[$key],
                    'datetime' => $data->date,
                    'note' => $data->note,
                    'amount' => $request->amount[$key],
                ];
                Transaction::create($transactionData);
            }
        }

        $request->session()->flash('successMessage', 'Loan was successfully added!');
        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show loan');

        $data = Loan::with('loanHolder', 'transactions.bank')->findOrFail($id);
        return view('admin.payment.loan-payment.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit loan');

        $data = Loan::with('transactions')->findOrFail($id);
        $banks = Bank::select(['id', 'bank_name'])->where('status', 'Active')->get();
        $loanHolders = LoanHolder::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        if ($data->type == 'Adjustment') {
            $adjustment = true;
            return view('admin.payment.loan-payment.edit', compact('data', 'loanHolders', 'adjustment'));
        }

        if ($data->transactions) {
            $items = $data->transactions;
        } else {
            $items = [
                (object) [
                    'id' => 0,
                    'bank_id' => null,
                    'amount' => null,
                ]
            ];
        }

        return view('admin.payment.loan-payment.edit', compact('data', 'banks', 'loanHolders', 'items'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit loan');

        $this->validate($request, [
            'loan_holder_id' => 'required|integer',
            'type' => 'required|in:Received,Payment,Adjustment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = Loan::findOrFail($id);

        $storeData = [
            'loan_holder_id' => $request->loan_holder_id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => Auth::user()->id,
        ];
        $data->update($storeData);
        
        Transaction::where('flagable_id', $data->id)->where('flagable_type', Loan::class)->delete();

        if ($data && $data->type != 'Adjustment') {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Loan',
                        'flagable_id' => $data->id,
                        'flagable_type' => Loan::class,
                        'bank_id' => $request->bank_id[$key],
                        'datetime' => $data->date,
                        'note' => $data->note,
                        'amount' => $request->amount[$key],
                    ];
                }
                Transaction::insert($transactionData);
        }

        $request->session()->flash('successMessage', 'Loan was successfully updated!');
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete loan');

        try {
            $data = Loan::findOrFail($id);
            Transaction::where('flagable_id', $data->id)->where('flagable_type', Loan::class)->delete();
            $data->delete();

            $request->session()->flash('successMessage', 'Loan was successfully deleted.');
        } catch (\Exception $e) {
            $request->session()->flash('errorMessage', 'Loan deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
