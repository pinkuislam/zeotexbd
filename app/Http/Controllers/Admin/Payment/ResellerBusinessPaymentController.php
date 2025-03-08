<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Transaction;
use App\Services\SMSService;
use Illuminate\Http\Request;
use App\Services\BankService;
use App\Services\CodeService;
use App\Models\ResellerBusinessPayment;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResellerBusinessPaymentController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list reseller-business-payments');

        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            $sql = ResellerBusinessPayment::with('bank', 'resellerBusiness')->orderBy('id', 'DESC');
        }else{
            $sql = ResellerBusinessPayment::with('bank', 'resellerBusiness')->where('created_by',auth()->user()->id)->orderBy('id', 'DESC');
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

        if ($request->reseller_business) {
            $sql->where('reseller_business_id', $request->reseller_business);
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
        $records = $sql->paginate($request->per_page_limit ?? config('settings.per_page_limit'));
        $banks = BankService::allBank(Auth::user());
        $reseller_business = User::where('role','Reseller Business')->where('status', 'Active')->get();

        return view('admin.payment.resellerbusiness.index', compact('records', 'banks', 'reseller_business'));
    }

    public function create()
    {
        $this->authorize('add reseller-business-payments');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null,
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $reseller_business = User::where('role','Reseller Business')->where('status', 'Active')->get();

        return view('admin.payment.resellerbusiness.create', compact('banks', 'reseller_business', 'items'))->with('type', 'Payment');
    }

    public function receive()
    {
        $this->authorize('add reseller-business-payments');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null,
            ]
        ];

        $banks = BankService::allBank(Auth::user());
          $reseller_business = User::where('role','Reseller Business')->where('status', 'Active')->get();

        return view('admin.payment.resellerbusiness.create', compact('banks', 'reseller_business', 'items'))->with('type', 'Received');
    }

    public function adjustment()
    {
        $this->authorize('add reseller-business-payments');

          $reseller_business = User::where('role','Reseller Business')->where('status', 'Active')->get();

        return view('admin.payment.resellerbusiness.adjustment', compact('reseller_business'));
    }

    public function store(Request $request)
    {
        $this->authorize('add reseller-business-payments');

        $this->validate($request, [
            'reseller_business_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $code = CodeService::generate(ResellerBusinessPayment::class, '', 'receipt_no');

        $storeData = [
            'reseller_business_id' => $request->reseller_business_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'receipt_no' => $code,
            'amount' => $request->total_amount,
            'note' => $request->note,
            'created_by' => Auth::user()->id,
        ];

        $data = ResellerBusinessPayment::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Reseller Business',
                        'flagable_id' => $data->id,
                        'flagable_type' => ResellerBusinessPayment::class,
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

        $request->session()->flash('successMessage', 'Reseller Business Payment was successfully added!');
        $route = $data->type == 'Received' ? 'receive' : ($data->type == 'Adjustment' ? 'adjustment' : 'create');
        return redirect()->route('admin.payment.reseller-business-payments.' . $route, qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show reseller-business-payments');

        $data = ResellerBusinessPayment::with('bank', 'resellerBusiness')->findOrFail($id);
        return view('admin.payment.resellerbusiness.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit reseller-business-payments');

        $data = ResellerBusinessPayment::findOrFail($id);

          $reseller_business = User::where('role','Reseller Business')->where('status', 'Active')->get();

        if ($data->type == 'Adjustment') {
            return view('admin.payment.resellerbusiness.adjustment', compact('data', 'reseller_business'));
        }

        $banks = BankService::allBank(Auth::user());

        $items = Transaction::where('flag', 'Reseller Business')->where('flagable_id', $data->id)->get();
        if ($items == null) {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'amount' => null,
                ]
            ];
        }

        return view('admin.payment.resellerbusiness.edit', compact('data', 'reseller_business', 'banks', 'items'))->with('type', $data->type);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit reseller-business-payments');

        $this->validate($request, [
            'reseller_business_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = ResellerBusinessPayment::findOrFail($id);

        $storeData = [
            'reseller_business_id' => $request->reseller_business_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', ResellerBusinessPayment::class)->delete();

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Reseller Business',
                        'flagable_id' => $data->id,
                        'flagable_type' => ResellerBusinessPayment::class,
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

        $request->session()->flash('successMessage', 'Reseller Business Payment was successfully updated!');
        return redirect()->route('admin.payment.reseller-business-payments.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete reseller-business-payments');

        $data = ResellerBusinessPayment::findOrFail($id);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', ResellerBusinessPayment::class)->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Reseller Business Payment was successfully deleted!');
        return redirect()->route('admin.payment.reseller-business-payments.index', qArray());
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('approval reseller-business-payments');

        
        
        $data = ResellerBusinessPayment::with('transactions')->findOrFail($id);
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
        }else{
            $flag = BankService::checkTransactionBankAccess(Auth::user(), $data->transactions, $data->type);
            if (!$flag || $data->created_by != auth()->user()->id) {
                $request->session()->flash('errorMessage', 'You have no access to Approve this!');
                return redirect()->route('admin.payment.reseller-business-payments.index');
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
        return redirect()->route('admin.payment.reseller-business-payments.index');
    }

    // public function sendSMS($data)
    // {
    //     $customerDue = CustomerService::due($data->reseller_business_id);
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