<?php

namespace App\Services;

use App\Models\ResellerPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CodeService;

class ResellerService
{
    public static function due($resellerId)
    {
        // Opening Due + Sele Reseller amount + Received  - (Payment + Sale Return reseller amount + Adjustment) 

        $condition = [
            'id' => null,
            'stock_id' => null,
        ];
        $reseller = User::select(DB::raw("((IFNULL(users.opening_due, 0) + IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - (IFNULL(C.amount, 0) + IFNULL(D.amount, 0) + IFNULL(F.amount, 0))) AS due"))
        ->leftJoin(DB::raw("(SELECT user_id, SUM(reseller_amount) AS amount FROM sales WHERE type = 'Reseller' GROUP BY user_id) AS A"), function($q) {
            $q->on('users.id', '=', 'A.user_id');
        })

        ->leftJoin(DB::raw("(SELECT reseller_id, SUM(total_amount) AS amount FROM reseller_payments WHERE type = 'Received' GROUP BY reseller_id) AS B"), function($q) {
            $q->on('users.id', '=', 'B.reseller_id');
        })

        ->leftJoin(DB::raw("(SELECT user_id, SUM(reseller_amount) AS amount FROM sale_returns GROUP BY user_id) AS C"), function($q) {
            $q->on('users.id', '=', 'C.user_id');
        })

        ->leftJoin(DB::raw("(SELECT user_id, SUM(deduction_amount) AS amount FROM sale_returns GROUP BY user_id) AS F"), function($q) {
            $q->on('users.id', '=', 'F.user_id');
        })

        ->leftJoin(DB::raw("(SELECT reseller_id, SUM(total_amount) AS amount FROM reseller_payments WHERE type != 'Received' GROUP BY reseller_id) AS D"), function($q) {
            $q->on('users.id', '=', 'D.reseller_id');
        })

        ->where('id', $resellerId)
        ->first();

        if ($reseller && $reseller->due) {
            return $reseller->due;
        }
        return 0;
    }

    public static function stockAdjustment($data, $stockId = null)
    {
        if ($stockId) {
            //Delete old adjustment...
            ResellerPayment::where('type', 'Adjustment')->where('stock_id', $stockId)->delete();
        }

        $code = CodeService::generate(ResellerPayment::class, '', 'receipt_no');

        $payData = [
            'reseller_id' => $data->reseller_id,
            'stock_id' => $data->id,
            'type' => 'Adjustment',
            'date' => $data->purchase_date,
            'receipt_no' => $code,
            'total_amount' => $data->adjust_amount,
            'total_cost' => 0,
            'total_transaction_amount' => $data->adjust_amount,
            'note' => $data->note,
            'created_by' => Auth::user()->id,
        ];
        ResellerPayment::create($payData);
    }
}