<?php

namespace App\Services;

use App\Models\SellerCommission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CodeService;

class SellerService
{
    public static function due($sellerId)
    {
        // Opening Due + Sele Commission + Received  - (Payment + Sale Return deduction amount + Adjustment) 

        $condition = [
            'id' => null,
            'stock_id' => null,
        ];
        $seller = User::select(DB::raw("((IFNULL(users.opening_due, 0) + IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - (IFNULL(C.amount, 0) + IFNULL(D.amount, 0))) AS due"))
        ->leftJoin(DB::raw("(SELECT user_id, SUM(commission_amount) AS amount FROM sales WHERE type = 'Seller' GROUP BY user_id) AS A"), function($q) {
            $q->on('users.id', '=', 'A.user_id');
        })

        ->leftJoin(DB::raw("(SELECT seller_id, SUM(total_amount) AS amount FROM seller_commissions WHERE type = 'Received' GROUP BY seller_id) AS B"), function($q) {
            $q->on('users.id', '=', 'B.seller_id');
        })

        ->leftJoin(DB::raw("(SELECT user_id, SUM(deduction_amount) AS amount FROM sale_returns GROUP BY user_id) AS C"), function($q) {
            $q->on('users.id', '=', 'C.user_id');
        })

        ->leftJoin(DB::raw("(SELECT seller_id, SUM(total_amount) AS amount FROM seller_commissions WHERE type != 'Received' GROUP BY seller_id) AS D"), function($q) {
            $q->on('users.id', '=', 'D.seller_id');
        })

        ->where('id', $sellerId)
        ->first();

        if ($seller && $seller->due) {
            return $seller->due;
        }
        return 0;
    }

    public static function stockAdjustment($data, $stockId = null)
    {
        if ($stockId) {
            //Delete old adjustment...
            SellerCommission::where('type', 'Adjustment')->where('stock_id', $stockId)->delete();
        }

        $code = CodeService::generate(SellerCommission::class, '', 'receipt_no');

        $payData = [
            'seller_id' => $data->seller_id,
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
        SellerCommission::create($payData);
    }
}