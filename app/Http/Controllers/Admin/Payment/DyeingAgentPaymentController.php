<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\BankService;
use App\Services\CodeService;
use App\Http\Controllers\Controller;
use App\Models\DyeingAgent;
use App\Models\DyeingPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class DyeingAgentPaymentController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list dyeing-agent-payment');

        $sql = DyeingPayment::with('dyeingAgent')->orderBy('id', 'DESC');

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

        if ($request->delivery_agent) {
            $sql->where('delivery-agent_id', $request->delivery_agent);
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
        $dyeingagents = DyeingAgent::where('status', 'Active')->get();

        return view('admin.payment.dyeing.index', compact('records', 'banks', 'dyeingagents'));
    }

    public function create()
    {
        $this->authorize('add dyeing-agent-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $dyeingagents = DyeingAgent::where('status', 'Active')->get();

        return view('admin.payment.dyeing.create', compact('banks', 'dyeingagents', 'items'))->with('type', 'Payment');
    }

    public function receive()
    {
        $this->authorize('add dyeing-agent-payment');

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'amount' => null
            ]
        ];

        $banks = BankService::allBank(Auth::user());
        $dyeingagents = DyeingAgent::where('status', 'Active')->get();

        return view('admin.payment.dyeing.create', compact('banks', 'dyeingagents', 'items'))->with('type', 'Received');
    }

    public function adjustment()
    {
        $this->authorize('add dyeing-agent-payment');

        $dyeingagents = DyeingAgent::where('status', 'Active')->get();

        return view('admin.payment.dyeing.adjustment', compact('dyeingagents'));
    }

    public function store(Request $request)
    {
        $this->authorize('add dyeing-agent-payment');

        $this->validate($request, [
            'dyeing_agent_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $code = CodeService::generate(DyeingPayment::class, '', 'receipt_no');

        $storeData = [
            'dyeing_agent_id' => $request->dyeing_agent_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'receipt_no' => $code,
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'created_by' => Auth::user()->id,
        ];
        $data = DyeingPayment::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Dyeing',
                        'flagable_id' => $data->id,
                        'flagable_type' => DyeingPayment::class,
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

        $request->session()->flash('successMessage', 'Dyeing Agent Payment was successfully added!');
        $route = $data->type == 'Received' ? 'receive' : ($data->type == 'Adjustment' ? 'adjustment' : 'create');
        return redirect()->route('admin.payment.dyeing-payments.' . $route, qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show dyeing-agent-payment');

        $data = DyeingPayment::with('dyeingAgent')->findOrFail($id);
        return view('admin.payment.dyeing.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit dyeing-agent-payment');
        
        $data = DyeingPayment::findOrFail($id);
        
        $dyeingagents = DyeingAgent::where('status', 'Active')->get();

        if ($data->type == 'Adjustment') {
            return view('admin.payment.dyeing.adjustment', compact('data', 'dyeingagents'));
        }

        $banks = BankService::allBank(Auth::user());

        $items = Transaction::where('flag', 'Dyeing')->where('flagable_id', $data->id)->get();

        if ($items == null) {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'amount' => null
                ]
            ];
        }

        return view('admin.payment.dyeing.edit', compact('data', 'dyeingagents', 'banks', 'items'))->with('type', $data->type);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit dyeing-agent-payment');

        $this->validate($request, [
            'dyeing_agent_id' => 'required|integer',
            'type' => 'required|in:Received,Adjustment,Payment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = DyeingPayment::findOrFail($id);

        $storeData = [
            'dyeing_agent_id' => $request->dyeing_agent_id,
            'type' => $request->type,
            'date' => dbDateFormat($request->date),
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => Auth::user()->id,
        ];
        $data->update($storeData);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\DeliveryAgentPayment')->delete();

        if ($data && $data->type != 'Adjustment') {
            if ($request->only('transaction_id')) {
                $transactionData = [];
                foreach ($request->transaction_id as $key => $tinId) {
                    $transactionData[] = [
                        'type' => $data->type,
                        'flag' => 'Dyeing',
                        'flagable_id' => $data->id,
                        'flagable_type' => DyeingPayment::class,
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

        $request->session()->flash('successMessage', 'Dyeing Agent Payment was successfully updated!');
        return redirect()->route('admin.payment.dyeing.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete dyeing-agent-payment');

        $data = DyeingPayment::findOrFail($id);

        Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\DeliveryAgentPayment')->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Dyeing Agent Payment was successfully deleted!');
        return redirect()->route('admin.payment.dyeing-payments.index', qArray());
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('approval dyeing-agent-payment');

        $data = DyeingPayment::with('transactions')->findOrFail($id);
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
        }else{
            $flag = BankService::checkTransactionBankAccess(Auth::user(), $data->transactions, $data->type);
            if (!$flag || $data->created_by != auth()->user()->id) {
                $request->session()->flash('errorMessage', 'You have no access to Approve this!');
                return redirect()->route('admin.payment.dyeing-payments.index');
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
            return redirect()->route('admin.payment.dyeing-payments.index', qArray());
        }
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->route('admin.payment.dyeing-payments.index');
    }
}