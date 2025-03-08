<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\BankService;
use App\Services\CodeService;
use App\Models\SellerCommission;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerCommissionController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list seller-payment');

        $sql = SellerCommission::with('seller')->orderBy('id', 'DESC');

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

        if ($request->seller) {
            $sql->where('seller_id', $request->seller);
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
        $sellers = User::where('role', 'Seller')->where('status', 'Active')->get();

        return view('admin.payment.seller.index', compact('records', 'banks', 'sellers'));
    }

    public function create()
    {
        $this->authorize('add seller-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $sellers = User::where('role', 'Seller')->where('status', 'Active')->get();

        return view('admin.payment.seller.create', compact('banks', 'sellers', 'items'))->with('type', 'Payment');
    }

    public function receive()
    {
        $this->authorize('add seller-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $sellers = User::where('role', 'Seller')->where('status', 'Active')->get();

        return view('admin.payment.seller.create', compact('banks', 'sellers', 'items'))->with('type', 'Received');
    }

    public function adjustment()
    {
        $this->authorize('add seller-payment');

        $sellers = User::where('role', 'Seller')->where('status', 'Active')->get();

        return view('admin.payment.seller.adjustment', compact('sellers'));
    }

    public function store(Request $request)
    {
        $this->authorize('add seller-payment');

        $this->validate($request, [
            'seller_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $code = CodeService::generate(SellerCommission::class, '', 'receipt_no');

        $storeData = [
            'seller_id' => $request->seller_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'receipt_no' => $code,
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'created_by' => Auth::user()->id,
        ];
        $data = SellerCommission::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'seller',
                        'flagable_id' => $data->id,
                        'flagable_type' => SellerCommission::class,
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

        $request->session()->flash('successMessage', 'Seller Payment was successfully added!');
        $route = $data->type == 'Received' ? 'receive' : ($data->type == 'Adjustment' ? 'adjustment' : 'create');
        return redirect()->route('admin.payment.seller-payments.' . $route, qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show seller-payment');

        $data = SellerCommission::with('seller')->findOrFail($id);
        return view('admin.payment.seller.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit seller-payment');
        
        $data = SellerCommission::findOrFail($id);
        
        $sellers = User::where('role', 'Seller')->where('status', 'Active')->get();

        if ($data->type == 'Adjustment') {
            return view('admin.payment.seller.adjustment', compact('data', 'sellers'));
        }

        $banks = BankService::allBank(Auth::user());

        $items = Transaction::where('flag', 'seller')->where('flagable_id', $data->id)->get();

        if ($items == null) {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'amount' => null
                ]
            ];
        }

        return view('admin.payment.seller.edit', compact('data', 'sellers', 'banks', 'items'))->with('type', $data->type);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit seller-payment');

        $this->validate($request, [
            'seller_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = SellerCommission::findOrFail($id);

        $storeData = [
            'seller_id' => $request->seller_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => Auth::user()->id,
        ];
        $data->update($storeData);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\SellerCommission')->delete();

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'seller',
                        'flagable_id' => $data->id,
                        'flagable_type' => SellerCommission::class,
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

        $request->session()->flash('successMessage', 'Seller Payment was successfully updated!');
        return redirect()->route('admin.payment.seller-payments.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete seller-payment');

        $data = SellerCommission::findOrFail($id);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\SellerCommission')->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Seller Payment was successfully deleted!');
        return redirect()->route('admin.payment.seller-payments.index', qArray());
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('approval seller-payment');

        $data = SellerCommission::with('transactions')->findOrFail($id);
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
        }else{
            $flag = BankService::checkTransactionBankAccess(Auth::user(), $data->transactions, $data->type);
            if (!$flag || $data->created_by != auth()->user()->id) {
                $request->session()->flash('errorMessage', 'You have no access to Approve this!');
                return redirect()->route('admin.payment.seller-payments.index');
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
            return redirect()->route('admin.payment.seller-payments.index', qArray());
        }
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('admin.payment.seller-payments.index');
    }
}
