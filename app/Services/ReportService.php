<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\StockItem;
use App\Models\LoanHolder;
use App\Models\StockReturn;
use App\Models\Transaction;
use App\Models\CustomerPayment;
use App\Models\SaleItemBarcode;
use App\Models\SupplierPayment;
use App\Models\StockItemBarcode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StockReturnItemBarcode;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public static function banks(): Builder
    {
        $sql = Bank::select(
            'banks.*',
            DB::raw('IFNULL(A.received_amount, 0) AS received_amount'), 
            DB::raw('IFNULL(B.payment_amount, 0) AS payment_amount'), 
            DB::raw('(IFNULL(A.received_amount, 0) - IFNULL(B.payment_amount, 0)) AS balance')
        );

        $sql->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS received_amount FROM `transactions` WHERE type = 'Received' GROUP BY bank_id) AS A"), function($q) {
            $q->on('A.bank_id', '=', 'banks.id');
        });
        $sql->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS payment_amount FROM `transactions` WHERE type='Payment' GROUP BY bank_id) AS B"), function($q) {
            $q->on('B.bank_id', '=', 'banks.id');
        })
        ->having('balance', '!=', 0);
        
        return $sql;
    }

    public static function customers(): Builder
    {
        $sql = Customer::select(
            'customers.id',
            'customers.route_id',
            'customers.code',
            'customers.name',
            'customers.contact_no',
            'customers.opening_due',
            'A.sale_amount',
            'A.discount_amount',
            'B.return_amount',
            'C.received_amount',
            'D.payment_amount',
            'E.adjustment_amount',
            DB::raw("((IFNULL(customers.opening_due, 0) + (IFNULL(A.sale_amount, 0) - IFNULL(A.discount_amount, 0)) + IFNULL(D.payment_amount, 0)) - (IFNULL(B.return_amount, 0) + IFNULL(C.received_amount, 0) + IFNULL(E.adjustment_amount, 0))) AS balance")
        );

        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(subtotal_amount) AS sale_amount, SUM(flat_discount_amount) AS discount_amount FROM sales GROUP BY customer_id) AS A"), function($q) {
            $q->on('customers.id', '=', 'A.customer_id');
        })
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS return_amount FROM stocks WHERE type = 'Return' GROUP BY customer_id) AS B"), function($q) {
            $q->on('customers.id', '=', 'B.customer_id');
        })
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_transaction_amount) AS received_amount FROM customer_payments WHERE type = 'Received' GROUP BY customer_id) AS C"), function($q) {
            $q->on('customers.id', '=', 'C.customer_id');
        })
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_transaction_amount) AS payment_amount FROM customer_payments WHERE type = 'Payment' GROUP BY customer_id) AS D"), function($q) {
            $q->on('customers.id', '=', 'D.customer_id');
        })
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_transaction_amount) AS adjustment_amount FROM customer_payments WHERE type = 'Adjustment' GROUP BY customer_id) AS E"), function($q) {
            $q->on('customers.id', '=', 'E.customer_id');
        })
        ->having('balance', '!=', 0);
        
        return $sql;
    }

    public static function suppliers(): Builder
    {
        $sql = Supplier::select(
            'suppliers.id',
            'suppliers.code',
            'suppliers.name',
            'suppliers.contact_no',
            'suppliers.opening_due',
            'A.stock_amount',
            'A.discount_amount',
            'B.return_amount',
            'C.received_amount',
            'D.payment_amount',
            'E.adjustment_amount',
            DB::raw("((IFNULL(suppliers.opening_due, 0) + (IFNULL(A.stock_amount, 0) - IFNULL(A.discount_amount, 0)) + IFNULL(C.received_amount, 0)) - (IFNULL(D.payment_amount, 0) + IFNULL(B.return_amount, 0) + IFNULL(E.adjustment_amount, 0))) AS balance")
        );

        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS stock_amount, SUM(flat_discount_amount) AS discount_amount FROM stocks WHERE type = 'In' GROUP BY supplier_id) AS A"), function($q) {
            $q->on('suppliers.id', '=', 'A.supplier_id');
        })
        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS return_amount FROM stock_returns GROUP BY supplier_id) AS B"), function($q) {
            $q->on('suppliers.id', '=', 'B.supplier_id');
        })
        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_transaction_amount) AS received_amount FROM supplier_payments WHERE type = 'Received' GROUP BY supplier_id) AS C"), function($q) {
            $q->on('suppliers.id', '=', 'C.supplier_id');
        })
        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_transaction_amount) AS payment_amount FROM supplier_payments WHERE type = 'Payment' GROUP BY supplier_id) AS D"), function($q) {
            $q->on('suppliers.id', '=', 'D.supplier_id');
        })
        ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_transaction_amount) AS adjustment_amount FROM supplier_payments WHERE type = 'Adjustment' GROUP BY supplier_id) AS E"), function($q) {
            $q->on('suppliers.id', '=', 'E.supplier_id');
        })
        ->having('balance', '!=', 0);
        
        return $sql;
    }

    public static function supplierStocks(): Builder
    {
        $sql = Supplier::select(
            'suppliers.id',
            'suppliers.code',
            'suppliers.name',
            'suppliers.contact_no',
            'A.balance',
        );

        $sql->leftJoin(DB::raw("(SELECT stocks.supplier_id, SUM(stock_item_barcodes.net_price) AS balance 
                FROM stocks 
                INNER JOIN stock_items ON (stocks.id = stock_items.stock_id)
                INNER JOIN stock_item_barcodes ON (stock_items.id = stock_item_barcodes.stock_item_id)
                LEFT JOIN stock_return_item_barcodes ON (stock_item_barcodes.id = stock_return_item_barcodes.stock_item_barcode_id)
                LEFT JOIN sale_item_barcodes ON (stock_item_barcodes.id = sale_item_barcodes.stock_item_barcode_id)
                WHERE stocks.type = 'In' AND stock_return_item_barcodes.id IS NULL AND sale_item_barcodes.id IS NULL 
                GROUP BY stocks.supplier_id
            ) AS A"), function($q) {
            $q->on('suppliers.id', '=', 'A.supplier_id');
        })
        ->where('balance', '!=', 0);
        
        return $sql;
    }
}