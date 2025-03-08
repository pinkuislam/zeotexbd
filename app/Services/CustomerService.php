<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CodeService;

class CustomerService
{
    public static function index($request)
    {
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            $sql = Customer::orderBy('name', 'ASC');
        }else{
            $sql = Customer::orderBy('name', 'ASC')->where('user_id',auth()->user()->id);
        }
        if ($request->q) {
            $sql->where('name', 'LIKE', '%'. $request->q . '%')
            ->orWhere('contact_name', 'LIKE', '%'. $request->q . '%')
            ->orWhere('mobile', 'LIKE', '%'. $request->q . '%')
            ->orWhere('address', 'LIKE', '%'. $request->q . '%')
            ->orWhere('email', 'LIKE', '%'. $request->q . '%');
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }
        return $sql->paginate($request->limit ?? config('settings.per_page_limit'));
    }
    public static function show($id)
    {
        return Customer::findOrFail($id);
    }
    public static function store($request)
    {
        $storeData = [
            'name' => $request->name,
            'contact_name' => $request->contact_person,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'shipping_address' => $request->shipping_address,
            'shipping_rate_id' => $request->shipping_rate_id,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ];
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            if ($request->type == "Admin") {
                $storeData['user_id'] = auth()->user()->id; 
            }else{
                $storeData['user_id'] = $request->user_id;
            }
            $storeData['type'] = $request->type;
        }else {
            $storeData['type'] =auth()->user()->role;
            $storeData['user_id'] = auth()->user()->id;
        }
        $data = Customer::create($storeData);
        return $data;
    }


    public static function update($request, $id)
    {
        $data = Customer::findOrFail($id);

        $storeData = [
            'name' => $request->name,
            'contact_name' => $request->contact_person,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'shipping_address' => $request->shipping_address,
            'shipping_rate_id' => $request->shipping_rate_id,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ];
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            if ($request->type == "Admin") {
                $storeData['user_id'] = auth()->user()->id; 
            }else{
                $storeData['user_id'] = $request->user_id;
            }
            $storeData['type'] = $request->type;
        }else {
            $storeData['type'] =auth()->user()->role;
            $storeData['user_id'] = auth()->user()->id;
        }
        $data->update($storeData);
        return $data;
    }

    public static function delete($id)
    {
        $data = Customer::findOrFail($id);
        $data->delete();
        return true;
    }
    public static function status($id)
    {
        $data = Customer::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);
        return $data;
    }

    public static function due($customerId, $saleId = null)
    {
        // (Sales + Payment) - (Sale Return + Received + Adjustment)

        $saleCondition = [
            'id' => null,
            'sale_id' => null,
        ];
        if ($saleId) {
            $saleCondition = [
                'id' => 'AND id != ' . $saleId,
                'sale_id' => 'WHERE sale_id != ' . $saleId,
            ];
        }
        
        $customer = Customer::select(
            DB::raw("((IFNULL(customers.opening_due, 0) + IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - (IFNULL(C.amount, 0) + IFNULL(D.amount, 0))) AS due")
        )
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS amount FROM sales ". $saleCondition['id'] ." GROUP BY customer_id) AS A"), function($q) {
            $q->on('customers.id', '=', 'A.customer_id');
        })

        ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS amount FROM customer_payments WHERE type = 'Payment' GROUP BY customer_id) AS B"), function($q) {
            $q->on('customers.id', '=', 'B.customer_id');
        })

        ->leftJoin(DB::raw("(SELECT customer_id, SUM(return_amount) AS amount FROM sale_returns  GROUP BY customer_id) AS C"), function($q) {
            $q->on('customers.id', '=', 'C.customer_id');
        })

        ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS amount FROM customer_payments WHERE type != 'Payment' GROUP BY customer_id) AS D"), function($q) {
            $q->on('customers.id', '=', 'D.customer_id');
        })

        ->where('id', $customerId)
        ->first();

        if ($customer && $customer->due) {
            return $customer->due;
        }
        return 0;
    }
    public static function saleAdjustment($data, $amount, $saleId = null)
    {
        if ($saleId) {
            //Delete old adjustment...
            CustomerPayment::where('type', 'Adjustment')->where('sale_id', $saleId)->first();
        }

        $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');

        $payData = [
            'customer_id' => $data->customer_id,
            'sale_id' => $data->id,
            'type' => 'Adjustment',
            'date' => $data->date,
            'receipt_no' => $code,
            'total_amount' => $amount,
            'total_cost' => 0,
            'total_transaction_amount' => $amount,
            'note' => $data->note,
            'created_by' => Auth::user()->id,
        ];
        CustomerPayment::create($payData);
    }

    public static function salePaymentReceived($data, $selectetBanks, $saleId = null)
    {
        if ($saleId) {
            //Delete old payment & transactions...
            $payment = CustomerPayment::where('type', 'Received')->where('sale_id', $saleId)->first();
            if ($payment) {
                $payment->delete();
                Transaction::where('flagable_id', $payment->id)->where('flagable_type', CustomerPayment::class)->delete();
            }
        }

        $totalPaidAmount = 0;
        $totalPaidCost = 0;
        foreach ($selectetBanks as $pay) {
            if ($pay['amount'] > 0 && $pay['bank_id'] > 0) {
                $totalPaidAmount += $pay['amount'];
                $totalPaidCost += $pay['cost'];
            }
        }

        if ($totalPaidAmount > 0) {
            $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');
            $payData = [
                'customer_id' => $data->customer_id,
                'sale_id' => $data->id,
                'type' => 'Received',
                'date' => $data->invoice_date,
                'receipt_no' => $code,
                'total_amount' => $totalPaidAmount,
                'total_cost' => $totalPaidCost,
                'total_transaction_amount' => ($totalPaidAmount - $totalPaidCost),
                'note' => $data->note,
                'created_by' => Auth::user()->id,
            ];
            $payment = CustomerPayment::create($payData);
            if ($payment) {
                foreach ($selectetBanks as $pay) {
                    if ($pay['amount'] > 0 && $pay['bank_id'] > 0) {
                        $transactionData[] = [
                            'type' => 'Received',
                            'flag' => 'Customer',
                            'flagable_id' => $payment->id,
                            'flagable_type' => CustomerPayment::class,
                            'note' => $data->note,
                            'bank_id' => $pay['bank_id'],
                            'datetime' => $data->invoice_date,
                            'amount' => $pay['amount'],
                            'cost' => $pay['cost'],
                            'transaction_amount' => ($pay['amount'] - $pay['cost']),
                            'created_by' => Auth::user()->id,
                            'created_at' => now(),
                        ];
                    }
                }
                
                if (isset($transactionData)) {
                    Transaction::insert($transactionData);
                }
            }
        }
    }
}