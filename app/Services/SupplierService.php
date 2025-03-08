<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CodeService;

class SupplierService
{
    public static function due($supplierId, $stockId = null)
    {
        // Opening Due + Stockin + Received  - (Payment + Return + Adjustment) 

        $condition = [
            'id' => null,
            'stock_id' => null,
        ];
        if ($stockId) {
            $condition = [
                'id' => 'AND id != ' . $stockId
            ];
        }
        $supplier = Supplier::select(DB::raw("((IFNULL(suppliers.opening_due, 0) + IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - (IFNULL(C.amount, 0) + IFNULL(D.amount, 0))) AS due"))
        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS amount FROM purchases WHERE type = 'Raw' GROUP BY supplier_id) AS A"), function($q) {
            $q->on('suppliers.id', '=', 'A.supplier_id');
        })

        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS amount FROM supplier_payments WHERE type = 'Received' GROUP BY supplier_id) AS B"), function($q) {
            $q->on('suppliers.id', '=', 'B.supplier_id');
        })

        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS amount FROM purchase_returns GROUP BY supplier_id) AS C"), function($q) {
            $q->on('suppliers.id', '=', 'C.supplier_id');
        })

        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS amount FROM supplier_payments WHERE type != 'Received' GROUP BY supplier_id) AS D"), function($q) {
            $q->on('suppliers.id', '=', 'D.supplier_id');
        })

        ->where('id', $supplierId)
        ->first();

        if ($supplier && $supplier->due) {
            return $supplier->due;
        }
        return 0;
    }

    public static function stockAdjustment($data, $purchaseId = null)
    {
        if ($purchaseId) {
            //Delete old adjustment...
            SupplierPayment::where('type', 'Adjustment')->where('purchase_id', $purchaseId)->delete();
        }

        $code = CodeService::generate(SupplierPayment::class, '', 'receipt_no');

        $payData = [
            'supplier_id' => $data->supplier_id,
            'purchase_id' => $data->id,
            'type' => 'Adjustment',
            'date' => dbDateFormat($data->date),
            'receipt_no' => $code,
            'total_amount' => $data->adjust_amount,
            'note' => $data->note,
            'created_by' => Auth::user()->id,
        ];
        SupplierPayment::create($payData);
    }
}