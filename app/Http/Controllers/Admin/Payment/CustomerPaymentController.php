<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Customer;
use App\Models\Transaction;
use App\Services\SMSService;
use Illuminate\Http\Request;
use App\Services\BankService;
use App\Services\CodeService;
use App\Models\CustomerPayment;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sale;
use App\Services\DeliveryAgentService;
use Illuminate\Support\Facades\Auth;

class CustomerPaymentController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list customer-payment');

        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            $sql = CustomerPayment::with('bank', 'customer')->orderBy('id', 'DESC');
        }else{
            $sql = CustomerPayment::with('bank', 'customer')->where('created_by',auth()->user()->id)->orderBy('id', 'DESC');
        }

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('receipt_no', 'LIKE', $request->q.'%')
                    ->orWhere('note', 'LIKE', $request->q.'%');
            });
        }

        if ($request->bank) {
            $sql->whereHas('transactions', function($q) use($request) {
                $q->where('bank_id', $request->bank);
            });
        }

        if ($request->customer) {
            $sql->where('customer_id', $request->customer);
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
        $customers = Customer::where('status', 'Active')->get();

        return view('admin.payment.customer.index', compact('records', 'banks', 'customers'));
    }

    public function create()
    {
        $this->authorize('add customer-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        // $customers = Customer::where('status', 'Active')->get();

        return view('admin.payment.customer.create', compact('banks', 'items'))->with('type', 'Payment');
    }

    public function receive()
    {
        $this->authorize('add customer-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null,
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        // $customers = Customer::where('status', 'Active')->get();

        return view('admin.payment.customer.create', compact('banks', 'items'))->with('type', 'Received');
    }

    public function bulkReceive()
    {
        $this->authorize('add customer-payment');

        $banks = BankService::allBank(Auth::user());
        $agents = DeliveryAgentService::allAgents(Auth::user());

        return view('admin.payment.customer.bulk-receive', compact('banks', 'agents'));
    }

    public function bulkReceiveStore(Request $request)
    {
        $this->authorize('add customer-payment');

       // $request->dd();

        $this->validate($request, [
            'date' => 'required|date',
            'order_id' => 'required|array',
            'order_id.*' => 'required|integer|distinct',
            'amount' => 'required|array',
            'amount.*' => 'nullable|numeric',
            'delivered.*' => 'nullable',
            'bank_id' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        foreach ($request->input('order_id') as $key => $item) {
            if (!isset($request->amount[$key])) {
                continue;
            }

            $order = Order::find($item);
            if (!$order) {
                continue;
            }

            $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');

            $data = CustomerPayment::create([
                'customer_id' => $order->customer_id,
                'type' => 'Received',
                'order_id' => $order->id,
                'date' => dbDateFormat($request->input('date')),
                'receipt_no' => $code,
                'amount' => $request->amount[$key],
                'note' => $request->input('note'),
                'created_by' => Auth::user()->id,
            ]);

            Transaction::create([
                'type' => $data->type,
                'flag' => 'Customer',
                'flagable_id' => $data->id,
                'flagable_type' => CustomerPayment::class,
                'note' => $data->note,
                'bank_id' => $request->input('bank_id'),
                'datetime' => $data->date,
                'amount' => $request->amount[$key],
                'created_by' => Auth::user()->id,
            ]);

            if (isset($request->delivered[$key])) {
                $sale = Sale::where('order_id', $order->id)->first();
                if($sale){
                    $sale->update([
                        'status' => 'Delivered',
                    ]);
                }
                $order->update([
                    'status' => 'Delivered',
                ]);
                if (isset($request->delivery_charge[$key])){
                    $sale->update([
                        'shipping_charge' => $request->delivery_charge[$key],
                        'delivery_agent_id' => $request->delivery_agent_id[$key],
                    ]);
                }

            }
        }

        $request->session()->flash('successMessage', 'Customer Payment was successfully added!');
        return redirect()->route('admin.payment.customer-payments.index', qArray());
    }

    public function adjustment()
    {
        $this->authorize('add customer-payment');

        $customers = Customer::where('status', 'Active')->get();

        return view('admin.payment.customer.adjustment', compact('customers'));
    }

    public function store(Request $request)
    {
        $this->authorize('add customer-payment');

        $this->validate($request, [
            'customer_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);
        if ($request->order_amount < $request->final_balance) {
            $request->session()->flash('errorMessage', 'Final Order Due will not be less than zero!');
            return back();
        }
        $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');

        $storeData = [
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'order_id' => $request->order_id,
            'date' => dbDateFormat($request->date),
            'receipt_no' => $code,
            'amount' => $request->total_amount,
            'note' => $request->note,
            'created_by' => Auth::user()->id,
        ];

        $data = CustomerPayment::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Customer',
                        'flagable_id' => $data->id,
                        'flagable_type' => CustomerPayment::class,
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
        //$this->sendSMS($data);

        $request->session()->flash('successMessage', 'Customer Payment was successfully added!');
        $route = $data->type == 'Received' ? 'receive' : ($data->type == 'Adjustment' ? 'adjustment' : 'create');
        return redirect()->route('admin.payment.customer-payments.' . $route, qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show customer-payment');

        $data = CustomerPayment::with('bank', 'customer')->findOrFail($id);
        return view('admin.payment.customer.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit customer-payment');

        $data = CustomerPayment::findOrFail($id);
        $order = Order::find($data->order_id);
        $customers = Customer::where('status', 'Active')->get();

        if ($data->type == 'Adjustment') {
            return view('admin.payment.customer.adjustment', compact('data', 'customers','order'));
        }

        $banks = BankService::allBank(Auth::user());

        $items = Transaction::where('flag', 'Customer')->where('flagable_id', $data->id)->get();
        if ($items == null) {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'amount' => null,
                ]
            ];
        }

        return view('admin.payment.customer.edit', compact('data', 'customers', 'banks', 'items','order'))->with('type', $data->type);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit customer-payment');

        $this->validate($request, [
            'customer_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);
        if ($request->order_amount < $request->final_balance) {
            $request->session()->flash('errorMessage', 'Final Order Due will not be less than zero!');
            return back();
        }
        $data = CustomerPayment::findOrFail($id);

        $storeData = [
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'order_id' => $request->order_id,
            'date' => dbDateFormat($request->date),
            'amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', CustomerPayment::class)->delete();

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Customer',
                        'flagable_id' => $data->id,
                        'flagable_type' => CustomerPayment::class,
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

        //$this->sendSMS($data);

        $request->session()->flash('successMessage', 'Customer Payment was successfully updated!');
        return redirect()->route('admin.payment.customer-payments.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete customer-payment');

        $data = CustomerPayment::findOrFail($id);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', CustomerPayment::class)->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Customer Payment was successfully deleted!');
        return redirect()->route('admin.payment.customer-payments.index', qArray());
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('approval customer-payment');

        $data = CustomerPayment::with('transactions')->findOrFail($id);
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Branch Admin')) {
        }else{
            $flag = BankService::checkTransactionBankAccess(Auth::user(), $data->transactions, $data->type);
            if (!$flag || $data->created_by != auth()->user()->id) {
                $request->session()->flash('errorMessage', 'You have no access to Approve this!');
                return redirect()->route('admin.payment.customer-payments.index');
            }
        }

        $approvedData = [
            'approved_at' => now(),
            'updated_by' => Auth::user()->id,
            'approved_by' => Auth::user()->id,
        ];

        $data->update($approvedData);
        $data->transactions()->update($approvedData);
        if ($data->customer && !$data->sale_id) {
            if ($data->customer->sending_sms == '1') {
                $this->sendSMS($data);
            }
        }
        //$this->sendSMS($data);
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('admin.payment.customer-payments.index');
    }

    // public function sendSMS($data)
    // {
    //     $customerDue = CustomerService::due($data->customer_id);
    //     $pay = 0;
    //     $received = 0;
    //     $adjust = 0;
    //     $previousDue = 0;
    //     $suffix = "\n" . $data->branch->sms_company_name . " " . $data->branch->sms_company_number;
    //     if($data->type == 'Payment'){
    //         $pay = $data->total_transaction_amount;
    //         $previousDue = $customerDue - $pay;
    //     } else if($data->type == 'Received'){
    //         $received = $data->total_transaction_amount;
    //         $previousDue = $customerDue + $received;
    //     }  else {
    //         $adjust = $data->total_transaction_amount;
    //         $previousDue = $customerDue + $adjust;
    //     }

    //     $sms = "No:".$data->receipt_no. "\nPrev Due:" . number_format($previousDue , 2);

    //     if($pay > 0){
    //         $sms = $sms . "\nPay:" . $pay . "\nRem. Due:". $customerDue . $suffix;
    //     } else if($received > 0){
    //         $sms = $sms . "\nReceived:" . $received . "\nRem. Due:". $customerDue . $suffix;
    //     } else{
    //         $sms = $sms . "\nAdjust:" . $adjust . "\nRem. Due:". $customerDue . $suffix;
    //     }

    //     SMSService::sendSMS('Customer', $data->customer->name, $sms, $data->customer->contact_no);


    // }
}

