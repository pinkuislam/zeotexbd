<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\ResellerBusinessPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CodeService;

class ResellerBusinessService
{
    public static function due($resellerBusinessID, $saleId = null)
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
        
        $reseller_business = User::select(
            DB::raw("((IFNULL(users.opening_due, 0) + IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - (IFNULL(C.amount, 0) + IFNULL(D.amount, 0))) AS due")
        )
        ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(total_amount) AS amount FROM sales ". $saleCondition['id'] ." GROUP BY reseller_business_id) AS A"), function($q) {
            $q->on('users.id', '=', 'A.reseller_business_id');
        })

        ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS amount FROM reseller_business_payments WHERE type = 'Payment' GROUP BY reseller_business_id) AS B"), function($q) {
            $q->on('users.id', '=', 'B.reseller_business_id');
        })

        ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(return_amount) AS amount FROM sale_returns  GROUP BY reseller_business_id) AS C"), function($q) {
            $q->on('users.id', '=', 'C.reseller_business_id');
        })

        ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS amount FROM reseller_business_payments WHERE type != 'Payment' GROUP BY reseller_business_id) AS D"), function($q) {
            $q->on('users.id', '=', 'D.reseller_business_id');
        })

        ->where('id', $resellerBusinessID)
        ->first();
        if ($reseller_business && $reseller_business->due) {
            return $reseller_business->due;
        }
        return 0;
    }

    public static function saleAdjustment($data, $amount, $saleId = null)
    {
        if ($saleId) {
            //Delete old adjustment...
            ResellerBusinessPayment::where('type', 'Adjustment')->where('sale_id', $saleId)->first();
        }

        $code = CodeService::generate(ResellerBusinessPayment::class, '', 'receipt_no');

        $payData = [
            'branch_id' => $data->branch_id,
            'reseller_business_id' => $data->reseller_business_id,
            'sale_id' => $data->id,
            'type' => 'Adjustment',
            'date' => $data->invoice_date,
            'receipt_no' => $code,
            'total_amount' => $amount,
            'total_cost' => 0,
            'total_transaction_amount' => $amount,
            'note' => $data->note,
            'created_by' => Auth::user()->id,
        ];
        ResellerBusinessPayment::create($payData);
    }

    public static function salePaymentReceived($data, $selectetBanks, $saleId = null)
    {
        if ($saleId) {
            //Delete old payment & transactions...
            $payment = ResellerBusinessPayment::where('type', 'Received')->where('sale_id', $saleId)->first();
            if ($payment) {
                $payment->delete();
                Transaction::where('flagable_id', $payment->id)->where('flagable_type', ResellerBusinessPayment::class)->delete();
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
            $code = CodeService::generate(ResellerBusinessPayment::class, '', 'receipt_no');
            $payData = [
                'branch_id' => $data->branch_id,
                'reseller_business_id' => $data->reseller_business_id,
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
            $payment = ResellerBusinessPayment::create($payData);
            if ($payment) {
                foreach ($selectetBanks as $pay) {
                    if ($pay['amount'] > 0 && $pay['bank_id'] > 0) {
                        $transactionData[] = [
                            'type' => 'Received',
                            'flag' => 'Reseller Business',
                            'flagable_id' => $payment->id,
                            'flagable_type' => ResellerBusinessPayment::class,
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