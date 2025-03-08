<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\BankService;
use App\Services\CodeService;
use App\Models\SupplierPayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list supplier-payment');

        $sql = SupplierPayment::with('supplier')->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('receipt_no', 'LIKE', $request->q.'%')
                    ->orWhere('note', 'LIKE', $request->q.'%');
            });
        }

        if ($request->bank) {
            $sql->whereHas('transactions', function($q) use($request) {
                $q->where('bank', $request->bank);
            });
        }

        if ($request->supplier) {
            $sql->where('supplier_id', $request->supplier);
        }

        if ($request->type) {
            $sql->where('type', $request->type);
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
        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.payment.supplier.index', compact('records', 'banks', 'suppliers'));
    }

    public function create()
    {
        $this->authorize('add supplier-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.payment.supplier.create', compact('banks', 'suppliers', 'items'))->with('type', 'Payment');
    }

    public function receive()
    {
        $this->authorize('add supplier-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.payment.supplier.create', compact('banks', 'suppliers', 'items'))->with('type', 'Received');
    }

    public function adjustment()
    {
        $this->authorize('add supplier-payment');

        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.payment.supplier.adjustment', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $this->authorize('add supplier-payment');

        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $code = CodeService::generate(SupplierPayment::class, '', 'receipt_no');

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'receipt_no' => $code,
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'created_by' => Auth::user()->id,
        ];
        $data = SupplierPayment::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Supplier',
                        'flagable_id' => $data->id,
                        'flagable_type' => SupplierPayment::class,
                        'note' => $data->note,
                        'bank_id' => $request->bank_id[$key],
                        'datetime' => $data->date,
                        'amount' => $request->amount[$key],
                        'created_by' => Auth::user()->id,
                        'created_at' => now(),
                    ];
                }
                Transaction::insert($transactionData);
            }
        }

        $request->session()->put('payDate', $request->date);

        $request->session()->flash('successMessage', 'Supplier Payment was successfully added!');
        $route = $data->type == 'Received' ? 'receive' : ($data->type == 'Adjustment' ? 'adjustment' : 'create');
        return redirect()->route('admin.payment.supplier-payments.' . $route, qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show supplier-payment');

        $data = SupplierPayment::with('supplier')->findOrFail($id);
        return view('admin.payment.supplier.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit supplier-payment');
        
        $data = SupplierPayment::findOrFail($id);
        
        $suppliers = Supplier::where('status', 'Active')->get();

        if ($data->type == 'Adjustment') {
            return view('admin.payment.supplier.adjustment', compact('data', 'suppliers'));
        }

        $banks = BankService::allBank(Auth::user());

        $items = Transaction::where('flag', 'Supplier')->where('flagable_id', $data->id)->get();

        if ($items == null) {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'amount' => null
                ]
            ];
        }

        return view('admin.payment.supplier.edit', compact('data', 'suppliers', 'banks', 'items'))->with('type', $data->type);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit supplier-payment');

        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = SupplierPayment::findOrFail($id);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => Auth::user()->id,
        ];
        $data->update($storeData);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Supplier',
                        'flagable_id' => $data->id,
                        'flagable_type' => SupplierPayment::class,
                        'note' => $data->note,
                        'bank_id' => $request->bank_id[$key],
                        'datetime' => $data->date,
                        'amount' => $request->amount[$key],
                        'created_by' => Auth::user()->id,
                        'created_at' => now(),
                    ];
                }
                Transaction::insert($transactionData);
            }
        }

        $request->session()->put('payDate', $request->date);

        $request->session()->flash('successMessage', 'Supplier Payment was successfully updated!');
        return redirect()->route('admin.payment.supplier-payments.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete supplier-payment');

        $data = SupplierPayment::findOrFail($id);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Supplier Payment was successfully deleted!');
        return redirect()->route('admin.payment.supplier-payments.index', qArray());
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('approval supplier-payment');

        $data = SupplierPayment::with('transactions')->findOrFail($id);
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
        }else{
            $flag = BankService::checkTransactionBankAccess(Auth::user(), $data->transactions, $data->type);
            if (!$flag || $data->created_by != auth()->user()->id) {
                $request->session()->flash('errorMessage', 'You have no access to Approve this!');
                return redirect()->route('admin.payment.supplier-payments.index');
            }
        }
        $approvedData = [
            'approved_at' => now(),
            'updated_by' => Auth::user()->id,
            'approved_by' => Auth::user()->id,
        ];
        DB::beginTransaction();
        try{
            $data->update($approvedData);
            $data->transactions()->update($approvedData);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Error Occured! ' . $e);
            return redirect()->route('admin.payment.supplier-payments.index', qArray());
        }
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('admin.payment.supplier-payments.index');
    }
}
