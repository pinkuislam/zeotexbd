<?php

namespace App\Http\Controllers\Admin\Report;

use App\Models\Bank;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DeliveryAgent;
use App\Models\DyeingAgent;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeExpenseCategory;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\Production;
use App\Models\ProductOut;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\ResellerPayment;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SellerCommission;
use App\Models\SupplierPayment;
use Illuminate\Support\Carbon;

class CommonController extends Controller
{
    public function dashboard(Request $request){

        $fourDaysAgo = Carbon::now()->subDays(4);

        $sale = Order::whereDate('created_at', '<=', $fourDaysAgo)->where('status','Ordered');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sale->where('created_by',auth()->user()->id);
        }
        $data['pending_fourDays_totalOrders'] = $sale->count();

        $sale = Sale::whereDate('created_at', '<=', $fourDaysAgo)->where('status','Processing');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sale->where('created_by',auth()->user()->id);
        }
        $data['processing_fourDays_totalSales'] = $sale->count();

        $sale = Sale::where('status','Delivered');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sale->where('created_by',auth()->user()->id);
        }
        $data['delivered_totalSales'] = $sale->count();

        
        $today = Carbon::now()->toDateString();

        $tsql = Order::whereDate('created_at', $today);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $tsql->where('user_id', auth()->user()->id);
        }
        $data['today_totalOrders']  = $tsql->count();

        $otsql = Order::where('status','Ordered')->whereDate('created_at', $today);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $otsql->where('user_id', auth()->user()->id);
        }
        $data['ordered_today_totalOrders']  = $otsql->count();

        $ptsql = Order::where('status','Processing')->whereDate('updated_at', $today);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $ptsql->where('user_id', auth()->user()->id);
        }
        $data['processing_today_totalOrders']  = $ptsql->count();

        $dtsql = Order::where('status','Delivered')->whereDate('updated_at', $today);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $dtsql->where('user_id', auth()->user()->id);
        }
        $data['delivered_today_totalOrders']  = $dtsql->count();

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $wsql = Order::whereBetween('created_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $wsql->where('user_id', auth()->user()->id);
        }
        $data['week_totalOrders']  = $wsql->count();

        $owsql = Order::where('status','Ordered')->whereBetween('created_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $owsql->where('user_id', auth()->user()->id);
        }
        $data['ordered_week_totalOrders']  = $owsql->count();

        $pwsql = Order::where('status','Processing')->whereBetween('updated_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $pwsql->where('user_id', auth()->user()->id);
        }
        $data['processing_week_totalOrders']  = $pwsql->count();

        $dwsql = Order::where('status','Delivered')->whereBetween('updated_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $dwsql->where('user_id', auth()->user()->id);
        }
        $data['delivered_week_totalOrders']  = $dwsql->count();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $msql = Order::whereBetween('created_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $msql->where('user_id', auth()->user()->id);
        }
        $data['month_totalOrders']  = $msql->count();

        $omsql = Order::where('status','Ordered')->whereBetween('created_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $omsql->where('user_id', auth()->user()->id);
        }
        $data['ordered_month_totalOrders']  = $omsql->count();

        $pmsql = Order::where('status','Processing')->whereBetween('updated_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $pmsql->where('user_id', auth()->user()->id);
        }
        $data['processing_month_totalOrders']  = $pmsql->count();

        $dmsql = Order::where('status','Delivered')->whereBetween('updated_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $dmsql->where('user_id', auth()->user()->id);
        }
        $data['delivered_month_totalOrders']  = $dmsql->count();

        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();

        $ysql = Order::whereBetween('created_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $ysql->where('user_id', auth()->user()->id);
        }
        $data['year_totalOrders']  = $ysql->count();

        $oysql = Order::where('status','Ordered')->whereBetween('created_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $oysql->where('user_id', auth()->user()->id);
        }
        $data['ordered_year_totalOrders']  = $oysql->count();

        $pysql = Order::where('status','Processing')->whereBetween('updated_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $pysql->where('user_id', auth()->user()->id);
        }
        $data['processing_year_totalOrders']  = $pysql->count();

        $dysql = Order::where('status','Delivered')->whereBetween('updated_at', [$startDate, $endDate]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $dysql->where('user_id', auth()->user()->id);
        }
        $data['delivered_year_totalOrders']  = $dysql->count();

        $data['totalSeller'] = User::where('role','Seller')->where('status','Active')->count();
        $sellers = User::where('role','Seller')->where('status','Active')->get('id');
        $total_seller_order_amount = 0;
        // $total_seller_due_amount = 0;
        foreach ($sellers as  $seller) {
            $total_seller_order_amount += $seller->totalCustomerOrderAmount();
            // $total_seller_due_amount += $seller->totalCustomerDue();
        }
        $data['totalReseller'] = User::where('role','Reseller')->where('status','Active')->count();
        $data['totalSeller_orderAmount'] = $total_seller_order_amount;
        $total_reseller_order_amount = 0;
        // $total_reseller_due_amount = 0;
        $resellers = User::where('role','Reseller')->where('status','Active')->get('id');
        foreach ($resellers as  $reseller) {
            $total_reseller_order_amount += $reseller->totalCustomerOrderAmount();
            // $total_reseller_due_amount += $reseller->totalCustomerDue();
        }
        $data['totalReseller_orderAmount'] = $total_reseller_order_amount;

        $topsql = ProductOut::select('product_id','color_id', DB::raw("COUNT(product_id) AS product_count"))
        ->with('product:id,code,name','color:id,name')
        ->leftjoin('sales' , 'sales.id','product_outs.flagable_id')
        ->where('product_outs.type','Sale')
        ->groupBy('product_outs.product_id','product_outs.color_id')
        ->orderBy(DB::raw("COUNT(product_id)"), 'DESC')
        ->limit(10);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $topsql->where('sales.user_id', auth()->user()->id);
        }
        $data['topSellingProducts'] = $topsql->get();

        $lowsql = ProductOut::select('product_id','color_id', DB::raw("COUNT(product_id) AS product_count"))
        ->with('product:id,code,name','color:id,name')
        ->leftjoin('sales' , 'sales.id','product_outs.flagable_id')
        ->where('product_outs.type','Sale')
        ->groupBy('product_outs.product_id','product_outs.color_id')
        ->orderBy(DB::raw("COUNT(product_id)"), 'ASC')
        ->limit(10);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $lowsql->where('sales.user_id', auth()->user()->id);
        }
        $data['lowSellingProducts'] = $lowsql->get();
        $monthlyOrderCharts = [];
        $orderSql = Order::select('id',DB::raw('MONTH(date) AS month'), DB::raw('SUM(amount) AS amount'))
            ->whereYear('date', date('Y'))
            ->groupBy(DB::raw('MONTH(date)'));
            if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
                $orderSql->where('user_id', auth()->user()->id);
            }
            $smsModelArr =  $orderSql->pluck('amount', 'month')->toArray();
        for ($x = 1; $x <= 12; $x++) {
            if (array_key_exists($x, $smsModelArr)) {
                $monthlyOrderCharts['Order Amount'][$x] = $smsModelArr[$x];
            } else {
                $monthlyOrderCharts['Order Amount'][$x] = 0;
            }
        }
        $data['monthlyOrderCharts'] = $monthlyOrderCharts;
        return view('admin.dashboard', $data);
    }
    public function bank(Request $request)
    {
        $reports = $this->bankList($request);
        if ($request['action'] == 'print') {
            $title = 'Bank Ladger';
            return view('admin.report.print.bank', compact('reports','title'));
        }
        return view('admin.report.bank', compact('reports'));
    }
    public function bankList($request){
        $sql = Bank::select('banks.*', DB::raw('IFNULL(A.inAmount, 0) AS inAmount'), DB::raw('IFNULL(B.outAmount, 0) AS outAmount'), DB::raw('(IFNULL(banks.opening_balance, 0) + IFNULL(A.inAmount, 0) - IFNULL(B.outAmount, 0)) AS balanceAmount'))->orderBy('bank_name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT transactions.bank_id, SUM(transactions.amount) AS inAmount FROM `transactions` WHERE transactions.type='Received' GROUP BY transactions.bank_id) AS A"), function($q) {
            $q->on('A.bank_id', '=', 'banks.id');
        });
        $sql->leftJoin(DB::raw("(SELECT transactions.bank_id, SUM(transactions.amount) AS outAmount FROM `transactions` WHERE transactions.type='Payment' GROUP BY transactions.bank_id) AS B"), function($q) {
            $q->on('B.bank_id', '=', 'banks.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('banks.code', 'LIKE','%'. $request->q.'%')
                ->orWhere('banks.bank_name', 'LIKE','%'. $request->q.'%')
                ->orWhere('banks.branch_name', 'LIKE','%'. $request->q.'%')
                ->orWhere('banks.account_name', 'LIKE','%'. $request->q.'%')
                ->orWhere('banks.account_no', 'LIKE','%'. $request->q.'%');
            });
        }

        return $sql->paginate($request->limit ?? config('settings.per_page_limit'));
    }
    public function bankTransactions(Request $request)
    {
        $banks = Bank::where('status', 'Active')->get();

        if ($request->bank == null) {
            return view('admin.report.bank-transactions', compact('banks'));
        }

        $data = Bank::find($request->bank);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('report.bank', qArray());
        }

        $sql = Transaction::orderBy('datetime', 'ASC')->where('bank_id', $data->id);

        if ($request->from) {
            $sql->whereDate('datetime', '>=', dbDateFormat($request->from));
        }
        if ($request->to) {
            $sql->whereDate('datetime', '<=', dbDateFormat($request->to));
        }

        $reports = $sql->get();

        // Opening Balance
        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        $sql = Bank::select('banks.*', DB::raw('IFNULL(A.inAmount, 0) AS inAmount'), DB::raw('IFNULL(B.outAmount, 0) AS outAmount'), DB::raw('(IFNULL(banks.opening_balance, 0) + IFNULL(A.inAmount, 0) - IFNULL(B.outAmount, 0)) AS balanceAmount'))->orderBy('bank_name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS inAmount FROM `transactions` WHERE Date(datetime) < '$from' AND type='Received' AND bank_id = $request->bank GROUP BY bank_id) AS A"), function($q) {
            $q->on('A.bank_id', '=', 'banks.id');
        });
        $sql->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS outAmount FROM `transactions` WHERE Date(datetime) < '$from' AND type='Payment' AND bank_id = $request->bank GROUP BY bank_id) AS B"), function($q) {
            $q->on('B.bank_id', '=', 'banks.id');
        });
        $openingBalance = $sql->where('id',$request->bank)->first();

        $openingBalance = $openingBalance->balanceAmount;
        if ($request['action'] == 'print') {
            $title = 'Bank Ladger Details';
            return view('admin.report.print.bank-transactions', compact('data', 'reports', 'banks','openingBalance','title'));
        }
        return view('admin.report.bank-transactions', compact('data', 'reports', 'banks','openingBalance'));
    }
    public function order(Request $request)
    {
        $sql = Order::orderBy('id', 'DESC')->with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'images',
            'customer',
            'resellerBusiness',
            'user',
            'sale',
            'createdBy',
            'updatedBy',
            'delivery',
            'shipping'
        ]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id',auth()->user()->id);
        }
        
        if ($request->customer_id) {
            $sql->where('customer_id', $request->customer_id);
        }
        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }
        if ($request->type) {
            $sql->where('type', $request->type);
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('amount', 'LIKE', '%'. $request->q.'%')
                ->orWhere('status', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('customer', function($q) use($request) {
                $q->where('name', $request->q);
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.product', function($q) use($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('resellerBusiness', function($q) use($request) {
                $q->where('name', $request->q);
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('user', function($q) use($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', $request->q);
            });
        }
        $result = $sql->paginate($request->limit ?? 250);
        // $result = $sql->where('code', 'SO00291')->first();
        // dd($result->customerPayment, $result->sale->saleConfirmPayment);

        $customer = Customer::select('id','name','mobile')->where('status','Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))){
            $customer->where('user_id',auth()->user()->id);
        }
        $customers = $customer->get();
        if ($request['action'] == 'print') {
            $title = 'Orders Ladger';
            return view('admin.report.print.order', compact('result','title'));
        }
        return view('admin.report.order', compact('result','customers'));
    }
    public function supplier(Request $request)
    {
        $reports = $this->totalSupplierDue($request);
        if ($request['action'] == 'print') {
            $title = 'Supplier Ladger';
            return view('admin.report.print.supplier', compact('reports','title'));
        }
        return view('admin.report.supplier', compact('reports'));
    }
    public function totalSupplierDue($request){

        $sql = Supplier::select(
            'suppliers.*',
            DB::raw('(IFNULL(A.stockAmount, 0) + IFNULL(F.accessoryStockAmount, 0)) AS stockAmount'),
            DB::raw('(IFNULL(B.returnAmount, 0) + IFNULL(G.accessoryReturnAmount, 0)) AS returnAmount'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),
            DB::raw('
                        (
                            IFNULL(suppliers.opening_due, 0) +
                                (
                                    IFNULL(A.stockAmount, 0) +
                                    IFNULL(F.accessoryStockAmount, 0) +
                                    IFNULL(C.receivedAmount, 0)
                                ) -
                                (
                                    IFNULL(B.returnAmount, 0) +
                                    IFNULL(G.accessoryReturnAmount, 0) +
                                    IFNULL(D.paidAmount, 0) +
                                    IFNULL(E.adjustmentAmount, 0)
                                )
                        ) AS dueAmount
                    ')
        )
        ->orderBy('name', 'ASC');

        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS stockAmount FROM `purchases` GROUP BY supplier_id) AS A"), function($q) {
            $q->on('A.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS returnAmount FROM `purchase_returns` GROUP BY supplier_id) AS B"), function($q) {
            $q->on('B.supplier_id', '=', 'suppliers.id');
        });
        
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS receivedAmount FROM `supplier_payments` WHERE type='Received' GROUP BY supplier_id) AS C"), function($q) {
            $q->on('C.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS paidAmount FROM `supplier_payments` WHERE type='Payment' GROUP BY supplier_id) AS D"), function($q) {
            $q->on('D.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS adjustmentAmount FROM `supplier_payments` WHERE type='Adjustment' GROUP BY supplier_id) AS E"), function($q) {
            $q->on('E.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS accessoryStockAmount FROM `accessory_stocks` GROUP BY supplier_id) AS F"), function($q) {
            $q->on('F.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS accessoryReturnAmount FROM `accessory_stock_returns` GROUP BY supplier_id) AS G"), function($q) {
            $q->on('G.supplier_id', '=', 'suppliers.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('suppliers.name', 'LIKE','%'. $request->q.'%')
                ->orWhere('suppliers.code', 'LIKE','%'. $request->q.'%')
                ->orWhere('suppliers.contact_no', 'LIKE','%'. $request->q.'%')
                ->orWhere('suppliers.address', 'LIKE','%'. $request->q.'%');
            });
        }

        $reports = $sql->where('status','Active')->get();

        return $reports;
    }
    public function seller(Request $request)
    {
        $reports = $this->totalSellerDue($request);
        if ($request['action'] == 'print') {
            $title = 'Seller Ladger';
            return view('admin.report.print.seller', compact('reports','title'));
        }
        return view('admin.report.seller', compact('reports'));
    }
    public function totalSellerDue($request){

        $dateFrom = $request->input('from') ?: '1970-01-01';
        $dateTo = $request->input('to')?: date('Y-m-d');

        $sql = User::select(
            'users.id',
            'users.code',
            'users.name',
            'users.mobile',
            'users.address',
            DB::raw('IFNULL(A.SaleAmount, 0) AS SaleAmount'),
            DB::raw('IFNULL(A.saleQuantity, 0) AS saleQuantity'),
            DB::raw('IFNULL(F.orderQuantity, 0) AS orderQuantity'),
            DB::raw('IFNULL(B.saleReturnAmount, 0) AS saleReturnAmount'),
            DB::raw('IFNULL(B.saleReturnQuantity, 0) AS saleReturnQuantity'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),

        )
        ->orderBy('name', 'ASC');

        $sql->leftJoin(DB::raw("(
        SELECT user_id,
        COUNT(id) as orderQuantity 
        FROM `orders`
        WHERE date BETWEEN '$dateFrom' AND '$dateTo'
        GROUP BY user_id) AS F"), function($q) {
            $q->on('F.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(
        SELECT user_id,
        COUNT(id) AS saleQuantity,
        SUM(total_amount) AS saleAmount
        FROM `sales`
        WHERE date BETWEEN '$dateFrom' AND '$dateTo'
        GROUP BY user_id) AS A"), function($q) {
            $q->on('A.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(
            SELECT user_id,
            COUNT(id) AS saleReturnQuantity,
            SUM(return_amount) AS saleReturnAmount
            FROM `sale_returns`
            WHERE date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY user_id) AS B"), function($q) {
            $q->on('B.user_id', '=', 'users.id');
        });

        $sql->leftJoin(DB::raw("(
            SELECT orders.user_id,
            SUM(customer_payments.amount) as receivedAmount
            FROM `orders`
            JOIN customer_payments ON customer_payments.order_id = orders.id
            WHERE customer_payments.type = 'Received' AND 
            customer_payments.date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY orders.user_id) AS C"), function($q) {
            $q->on('C.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(
            SELECT orders.user_id,
            SUM(customer_payments.amount) as paidAmount
            FROM `orders`
            JOIN customer_payments ON customer_payments.order_id = orders.id
            WHERE customer_payments.type = 'Payment' AND 
            customer_payments.date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY orders.user_id) AS D"), function($q) {
            $q->on('D.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(
            SELECT orders.user_id,
            SUM(customer_payments.amount) as adjustmentAmount
            FROM `orders`
            JOIN customer_payments ON customer_payments.order_id = orders.id
            WHERE customer_payments.type = 'Adjustment' AND 
            customer_payments.date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY orders.user_id) AS E"), function($q) {
            $q->on('E.user_id', '=', 'users.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('users.name', 'LIKE', $request->q.'%')
                ->orWhere('users.code', 'LIKE', $request->q.'%')
                ->orWhere('users.mobile', 'LIKE', $request->q.'%')
                ->orWhere('users.address', 'LIKE', $request->q.'%');
            });
        }
        $sql->where('role','Seller');
        $sql->where('status','Active');
        return $sql->get();
    }
    public function reseller(Request $request)
    {
        $reports = $this->totalResellerDue($request);
        if ($request['action'] == 'print') {
            $title = 'Reseller Ladger';
            return view('admin.report.print.reseller', compact('reports','title'));
        }
        return view('admin.report.reseller', compact('reports'));
    }
    public function totalResellerDue($request){

         $dateFrom = $request->input('from') ?: '1970-01-01';
        $dateTo = $request->input('to')?: date('Y-m-d');

        $sql = User::select(
            'users.id',
            'users.code',
            'users.name',
            'users.mobile',
            'users.address',
            DB::raw('IFNULL(F.SaleAmount, 0) AS SaleAmount'),
            DB::raw('IFNULL(A.resellerProfitAmount, 0) AS resellerProfitAmount'),
            DB::raw('IFNULL(A.saleQuantity, 0) AS saleQuantity'),
            DB::raw('IFNULL(C.orderQuantity, 0) AS orderQuantity'),
            DB::raw('IFNULL(B.saleReturnAmount, 0) AS saleReturnAmount'),
            DB::raw('IFNULL(B.saleReturnQuantity, 0) AS saleReturnQuantity'),
            DB::raw('IFNULL(D.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(F.SaleAmount, 0) - IFNULL(D.receivedAmount, 0) AS orderDueAmount'),
            DB::raw('IFNULL(E.resellerPaymentAmount, 0) AS resellerPaymentAmount'),
            DB::raw('IFNULL(F.resellerAmount, 0) AS resellerAmount'),
            DB::raw('IFNULL(A.resellerProfitAmount, 0) - IFNULL(D.receivedAmount, 0) AS resellerDueAmount')
        )
        ->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(
        SELECT user_id,
        COUNT(id) AS saleQuantity,
        SUM(reseller_amount) AS resellerProfitAmount
        FROM `sales`
        WHERE date BETWEEN '$dateFrom' AND '$dateTo'
        GROUP BY user_id) AS A"), function($q) {
            $q->on('A.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(
            SELECT user_id,
            COUNT(id) AS saleReturnQuantity,
            SUM(return_amount) AS saleReturnAmount
            FROM `sale_returns`
            WHERE date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY user_id) AS B"), function($q) {
            $q->on('B.user_id', '=', 'users.id');
        });

        $sql->leftJoin(DB::raw("(
        SELECT user_id,
        COUNT(id) as orderQuantity 
        FROM `orders`
        WHERE date BETWEEN '$dateFrom' AND '$dateTo'
        GROUP BY user_id) AS C"), function($q) {
            $q->on('C.user_id', '=', 'users.id');
        });

        $sql->leftJoin(DB::raw("(
            SELECT orders.user_id,
            SUM(customer_payments.amount) as receivedAmount
            FROM `orders`
            JOIN customer_payments ON customer_payments.order_id = orders.id
            WHERE customer_payments.type = 'Received' AND 
            customer_payments.date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY orders.user_id) AS D"), function($q) {
                $q->on('D.user_id', '=', 'users.id');
            });
            $sql->leftJoin(DB::raw("(
                SELECT reseller_id, 
                SUM(total_amount) AS resellerPaymentAmount 
                FROM `reseller_payments` WHERE type='Payment' AND
                reseller_payments.date BETWEEN '$dateFrom' AND '$dateTo'
                GROUP BY reseller_id) AS E
        "), function($q) {
            $q->on('E.reseller_id', '=', 'users.id');
        });
        
        $sql->leftJoin(DB::raw("(
            SELECT sales.user_id,
            SUM(product_outs.total_price) AS SaleAmount,
            SUM(product_outs.reseller_total_price) AS resellerAmount
            FROM `sales` JOIN product_outs ON product_outs.flagable_id=sales.id 
            WHERE product_outs.type='Sale' AND
            sales.date BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY sales.user_id
            ) AS F"), function($q) {
            $q->on('F.user_id', '=', 'users.id');
        });
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('users.name', 'LIKE','%'. $request->q.'%')
                ->orWhere('users.code', 'LIKE','%'. $request->q.'%')
                ->orWhere('users.mobile', 'LIKE','%'. $request->q.'%')
                ->orWhere('users.address', 'LIKE','%'. $request->q.'%');
            });
        }
        $sql->where('role','Reseller');
        $sql->where('status','Active');
        return $sql->get();
    }

    public function customer(Request $request)
    {
        $reports = $this->totalCustomerDue($request);
        if ($request['action'] == 'print') {
            $title = 'Customer Ladger';
            return view('admin.report.print.customer', compact('reports','title'));
        }
        return view('admin.report.customer', compact('reports'));
    }
    public function totalCustomerDue($request){

        $sql = Customer::select(
            'customers.id',
            'customers.name',
            'customers.mobile',
            'customers.address',
            DB::raw('IFNULL(A.saleAmount, 0) AS saleAmount'),
            DB::raw('IFNULL(A.discountAmount, 0) AS discountAmount'),
            DB::raw('IFNULL(B.returnAmount, 0) AS returnAmount'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),
            DB::raw('
                        (
                            IFNULL(A.shippingChargeAmount, 0) +
                            IFNULL(A.extraShippingChargeAmount, 0)
                        ) AS TotalShippingAmount
                    '),
            DB::raw('
                        (
                            IFNULL(customers.opening_due, 0) +
                                (
                                    IFNULL(A.saleAmount, 0) +
                                    IFNULL(D.paidAmount, 0)
                                ) -
                                (
                                    IFNULL(B.returnAmount, 0) +
                                    IFNULL(C.receivedAmount, 0) +
                                    IFNULL(E.adjustmentAmount, 0)
                                )
                        ) AS dueAmount
                    ')
        )
        ->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT customer_id,
         SUM(total_amount) AS saleAmount, 
            SUM(discount_amount) AS discountAmount , 
            SUM(shipping_charge + extra_shipping_charge) AS shippingChargeAmount, 
            SUM(extra_shipping_charge) AS extraShippingChargeAmount 
            FROM `sales` GROUP BY customer_id) AS A"), function($q) {
                $q->on('A.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(return_amount) AS returnAmount FROM `sale_returns` GROUP BY customer_id) AS B"), function($q) {
            $q->on('B.customer_id', '=', 'customers.id');
        });

        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS receivedAmount FROM `customer_payments` WHERE type='Received' GROUP BY customer_id) AS C"), function($q) {
            $q->on('C.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS paidAmount FROM `customer_payments` WHERE type='Payment' GROUP BY customer_id) AS D"), function($q) {
            $q->on('D.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS adjustmentAmount FROM `customer_payments` WHERE type='Adjustment' GROUP BY customer_id) AS E"), function($q) {
            $q->on('E.customer_id', '=', 'customers.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('customers.name', 'LIKE','%'. $request->q.'%')
                ->orWhere('customers.address', 'LIKE','%'. $request->q.'%')
                ->orWhere('customers.mobile', 'LIKE','%'. $request->q.'%');
            });
        }
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))){
            $sql->where('user_id', auth()->user()->id);
        }
        return $sql->where('status','Active')->paginate($request->limit ?? 500);
    }
    public function resellerBusiness(Request $request)
    {
        $reports = $this->totalResellerBusinessDue($request);
        if ($request['action'] == 'print') {
            $title = 'Reseller Business Ladger';
            return view('admin.report.print.reseller-business', compact('reports','title'));
        }
        return view('admin.report.reseller-business', compact('reports'));
    }
    public function totalResellerBusinessDue($request){
        $sales = '';
        if($request->date <> ''){
            $sales .= ' WHERE date <= "'.$request->date.'"';
        }
        $saleReturn = '';
        if($request->date <> ''){
            $saleReturn .= ' WHERE date <= "'.$request->date.'"';
        }
        $payment = '';
        if($request->date <> ''){
            $payment .= ' AND date <= "'.$request->date.'"';
        }
        $sql = User::select(
            'users.*',
            DB::raw('IFNULL(A.saleAmount, 0) AS saleAmount'),
            DB::raw('IFNULL(A.shippingChargeAmount, 0) AS shippingChargeAmount'),
            DB::raw('IFNULL(B.returnAmount, 0) AS returnAmount'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),
            DB::raw('
                        (
                            IFNULL(users.opening_due, 0) +
                                (
                                    IFNULL(A.saleAmount, 0) +
                                    IFNULL(D.paidAmount, 0) +
                                    IFNULL(A.shippingChargeAmount, 0)
                                ) -
                                (
                                    IFNULL(B.returnAmount, 0) +
                                    IFNULL(C.receivedAmount, 0) +
                                    IFNULL(E.adjustmentAmount, 0)
                                )
                        ) AS dueAmount
                    ')
        )
        ->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(total_amount) AS saleAmount, SUM(shipping_charge + extra_shipping_charge) AS shippingChargeAmount FROM `sales` $sales GROUP BY reseller_business_id) AS A"), function($q) {
            $q->on('A.reseller_business_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(return_amount) AS returnAmount FROM `sale_returns` $saleReturn GROUP BY reseller_business_id) AS B"), function($q) {
            $q->on('B.reseller_business_id', '=', 'users.id');
        });

        $sql->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS receivedAmount FROM `reseller_business_payments` WHERE type='Received' $payment GROUP BY reseller_business_id) AS C"), function($q) {
            $q->on('C.reseller_business_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS paidAmount FROM `reseller_business_payments` WHERE type='Payment' $payment GROUP BY reseller_business_id) AS D"), function($q) {
            $q->on('D.reseller_business_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS adjustmentAmount FROM `reseller_business_payments` WHERE type='Adjustment' $payment GROUP BY reseller_business_id) AS E"), function($q) {
            $q->on('E.reseller_business_id', '=', 'users.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('users.name', 'LIKE', $request->q.'%')
                ->orWhere('users.address', 'LIKE', $request->q.'%')
                ->orWhere('users.mobile', 'LIKE', $request->q.'%')
                ->orWhere('users.email', 'LIKE', $request->q.'%');
            });
        }
        $sql->where('role','Reseller Business');
        $reports = $sql->get();

        return $reports;
    }


    public function deliveryAgent(Request $request)
    {
        $reports = $this->totaldeliveryAgentDue($request);
        if ($request['action'] == 'print') {
            $title = 'Delivery Agent Ladger';
            return view('admin.report.print.delivery-agent', compact('reports','title'));
        }
        return view('admin.report.delivery-agent', compact('reports'));
    }
    public function totaldeliveryAgentDue($request){
        $grn = $purchaseReturn = $payment = '';
        if($request->date <> ''){
            $grn .= ' WHERE date <= "'.$request->date.'"';
        }
        if($request->date <> ''){
            $purchaseReturn .= ' WHERE date <= "'.$request->date.'"';
        }
        if($request->date <> ''){
            $payment .= ' AND date <= "'.$request->date.'"';
        }

        $sql = DeliveryAgent::select(
            'delivery_agents.*',
            DB::raw('IFNULL(A.shippingChargeAmount, 0) + IFNULL(A.extraShippingChargeAmount, 0) AS shippingChargeAmount'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),
            DB::raw('
                        (
                            IFNULL(delivery_agents.opening_due, 0) +
                                (
                                    IFNULL(A.shippingChargeAmount, 0) +
                                    IFNULL(A.extraShippingChargeAmount, 0) +
                                    IFNULL(C.receivedAmount, 0)
                                ) -
                                (
                                    IFNULL(D.paidAmount, 0) +
                                    IFNULL(E.adjustmentAmount, 0)
                                )
                        ) AS dueAmount
                    ')
        )
        ->orderBy('name', 'ASC');

        $sql->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(shipping_charge) AS shippingChargeAmount , SUM(extra_shipping_charge) AS extraShippingChargeAmount FROM `sales` $grn GROUP BY delivery_agent_id) AS A"), function($q) {
            $q->on('A.delivery_agent_id', '=', 'delivery_agents.id');
        });

        $sql->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS receivedAmount FROM `delivery_agent_payments` WHERE type='Received' $payment GROUP BY delivery_agent_id) AS C"), function($q) {
            $q->on('C.delivery_agent_id', '=', 'delivery_agents.id');
        });
        $sql->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS paidAmount FROM `delivery_agent_payments` WHERE type='Payment' $payment GROUP BY delivery_agent_id) AS D"), function($q) {
            $q->on('D.delivery_agent_id', '=', 'delivery_agents.id');
        });
        $sql->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS adjustmentAmount FROM `delivery_agent_payments` WHERE type='Adjustment' $payment GROUP BY delivery_agent_id) AS E"), function($q) {
            $q->on('E.delivery_agent_id', '=', 'delivery_agents.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('delivery_agents.name', 'LIKE', $request->q.'%')
                ->orWhere('delivery_agents.code', 'LIKE', $request->q.'%')
                ->orWhere('delivery_agents.mobile', 'LIKE', $request->q.'%')
                ->orWhere('delivery_agents.type', 'LIKE', $request->q.'%');
            });
        }

        $reports = $sql->get();

        return $reports;
    }
    public function expense(Request $request)
    {
        $reports = $this->total_expense_due($request);

        $incomeExpense = IncomeExpenseCategory::where('type','Expense')->get(['id','name']);
        $banks = Bank::where('status','Active')->get(['id','bank_name']);
        if ($request['action'] == 'print') {
            $title = 'Expense Ladger';
            return view('admin.report.print.expense', compact('reports','title'));
        }
        return view('admin.report.expense', compact('reports','incomeExpense','banks'));
    }

    public function total_expense_due($request)
    {
        $bankQry = '';
        if($request->bank_id > 0){
            $bankQry = ' AND transactions.bank_id = '.$request->bank_id;
        }
        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= " AND expenses.date >= '".dbDateFormat($request->from)."'";
        }
        if ($request->to) {
            $dateCond .= " AND expenses.date <= '".dbDateFormat($request->to)."'";
        }

        $sql = IncomeExpenseCategory::select(
                                'income_expense_categories.name as incomeExpenseName',
                                'A.*')->orderBy('A.date', 'DESC');
        $sql->leftJoin(DB::raw("(SELECT expenses.*,
                                        banks.bank_name,banks.account_no
                                    FROM
                                        `expenses`
                                    INNER JOIN
                                        transactions ON expenses.id = transactions.flagable_id
                                    INNER JOIN
                                        banks ON transactions.bank_id = banks.id
                                        WHERE
                                        transactions.flag='Expense'
                                        $bankQry
                                        $dateCond
                                ) AS A"), function($q) {
            $q->on('A.category_id', '=', 'income_expense_categories.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('income_expense_categories.name', 'LIKE', $request->q.'%');
            });
        }
        if($request->income_expense_id > 0){
            $sql->where('income_expense_categories.id',$request->income_expense_id);
        }
        $sql->where('income_expense_categories.type','Expense');
        $reports = $sql->having('A.amount', '>', 0)->get();

        return $reports;
    }
    public function income(Request $request)
    {
        $reports = $this->incomeList($request);

        $incomeExpense = IncomeExpenseCategory::where('type','Income')->get();
        $banks = Bank::where('status','Active')->get(['id','bank_name']);
        if ($request['action'] == 'print') {
            $title = 'Income Ladger';
            return view('admin.report.print.income', compact('reports','title'));
        }
        return view('admin.report.income', compact('reports','incomeExpense','banks'));
    }
    public function incomeList($request){
        $bankQry = '';
        if($request->bank_id > 0){
            $bankQry = ' AND transactions.bank_id = '.$request->bank_id;
        }
        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= " AND incomes.date >= '".dbDateFormat($request->from)."'";
        }
        if ($request->to) {
            $dateCond .= " AND incomes.date <= '".dbDateFormat($request->to)."'";
        }

        $sql = IncomeExpenseCategory::select(
                                'income_expense_categories.name as incomeExpenseName',
                                'A.*')->orderBy('A.date', 'DESC');
        $sql->leftJoin(DB::raw("(SELECT incomes.*,
                                        banks.bank_name,banks.account_no
                                    FROM
                                        `incomes`
                                    INNER JOIN
                                        transactions ON incomes.id = transactions.flagable_id
                                    INNER JOIN
                                        banks ON transactions.bank_id = banks.id
                                        WHERE
                                        transactions.flag='Income'
                                        $bankQry
                                        $dateCond
                                ) AS A"), function($q) {
            $q->on('A.category_id', '=', 'income_expense_categories.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('income_expense_categories.name', 'LIKE', $request->q.'%');
            });
        }
        if($request->income_expense_id > 0){
            $sql->where('income_expense_categories.id',$request->income_expense_id);
        }
        $sql->where('income_expense_categories.type','Income');
        $reports = $sql->having('A.amount', '>', 0)->get();
        return $reports;
    }
    public function customerTransactions(Request $request)
    {
        $customers = Customer::where('status','Active')->get();

        if ($request->customer == null) {
            return view('admin.report.customer-transactions', compact('customers'));
        }

        $data = Customer::find($request->customer);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.customer', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        // Opening Balance
        $sql = Customer::select(DB::raw("((IFNULL(customers.opening_due, 0) + IFNULL(A.sale_amount, 0) + IFNULL(D.payment, 0)) - (IFNULL(B.return_amount, 0) + IFNULL(C.received, 0) + IFNULL(E.adjustment, 0))) AS balance"))
                ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS sale_amount FROM sales WHERE date < '$from' AND customer_id = $request->customer GROUP BY customer_id) AS A"), function($q) {
                    $q->on('A.customer_id', '=', 'customers.id');
                })
                ->leftJoin(DB::raw("(SELECT customer_id, SUM(return_amount) AS return_amount FROM sale_returns WHERE date < '$from' AND customer_id = $request->customer GROUP BY customer_id) AS B"), function($q) {
                    $q->on('B.customer_id', '=', 'customers.id');
                })
                ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS received FROM customer_payments WHERE date < '$from' AND type='Received' AND customer_id = $request->customer GROUP BY customer_id) AS C"), function($q) {
                    $q->on('C.customer_id', '=', 'customers.id');
                })
                ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS payment FROM customer_payments WHERE date < '$from' AND type='Payment' AND customer_id = $request->customer GROUP BY customer_id) AS D"), function($q) {
                    $q->on('D.customer_id', '=', 'customers.id');
                })
                ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS adjustment FROM customer_payments WHERE date < '$from' AND type='Adjustment' AND customer_id = $request->customer GROUP BY customer_id) AS E"), function($q) {
                    $q->on('E.customer_id', '=', 'customers.id');
                })
                ->where('id',$request->customer);
        $openingBalance = $sql->first();

        // Report Lists
        $query1 = "SELECT `code`,`date`,'Sale' AS type,note, 'admin.sale.sales.show' AS route, id,  total_amount AS amount FROM sales AS X WHERE customer_id = $request->customer $dateCond";
        $query2 = "SELECT `code`,`date`,'Sale Return' AS type,note, 'admin.sale.return.show' AS route, id, return_amount AS amount FROM sale_returns AS X WHERE customer_id = $request->customer $dateCond";
        $query3 = "SELECT `receipt_no` AS code,`date`,'Received' AS type,note, 'admin.payment.customer-payments.show' AS route, id, amount FROM customer_payments AS X WHERE type='Received' AND customer_id = $request->customer $dateCond";
        $query4 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.customer-payments.show' AS route, id, amount FROM customer_payments AS X WHERE type='Payment' AND customer_id = $request->customer $dateCond";
        $query5 = "SELECT `receipt_no` AS code,`date`,'Adjustment' AS type,note, 'admin.payment.customer-payments.show' AS route, id, amount FROM customer_payments AS X WHERE type='Adjustment' AND customer_id = $request->customer $dateCond";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5) S ORDER BY S.`date` ASC");
        if ($request['action'] == 'print') {
            $title = 'Customer Ladger Details';
            return view('admin.report.print.customer-transactions', compact('data', 'reports', 'customers','openingBalance','title'));
        }
        return view('admin.report.customer-transactions', compact('data', 'reports', 'customers','openingBalance'));
    }
    public function resellerBusinessTransactions(Request $request)
    {
        $reseller_businesss = User::where('role','Reseller Business')->where('status','Active')->get();

        if ($request->reseller_business == null) {
            return view('admin.report.reseller-business-transactions', compact('reseller_businesss'));
        }

        $data = User::find($request->reseller_business);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.reseller-business', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        // Opening Balance
        $sql = User::select(DB::raw("((IFNULL(users.opening_due, 0) + IFNULL(A.sale_amount, 0) + IFNULL(D.payment, 0)) - (IFNULL(B.return_amount, 0) + IFNULL(C.received, 0) + IFNULL(E.adjustment, 0))) AS balance"))
                ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(total_amount) AS sale_amount FROM sales WHERE date < '$from' AND reseller_business_id = $request->reseller_business GROUP BY reseller_business_id) AS A"), function($q) {
                    $q->on('A.reseller_business_id', '=', 'users.id');
                })
                ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(return_amount) AS return_amount FROM sale_returns WHERE date < '$from' AND reseller_business_id = $request->reseller_business GROUP BY reseller_business_id) AS B"), function($q) {
                    $q->on('B.reseller_business_id', '=', 'users.id');
                })
                ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS received FROM reseller_business_payments WHERE date < '$from' AND type='Received' AND reseller_business_id = $request->reseller_business GROUP BY reseller_business_id) AS C"), function($q) {
                    $q->on('C.reseller_business_id', '=', 'users.id');
                })
                ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS payment FROM reseller_business_payments WHERE date < '$from' AND type='Payment' AND reseller_business_id = $request->reseller_business GROUP BY reseller_business_id) AS D"), function($q) {
                    $q->on('D.reseller_business_id', '=', 'users.id');
                })
                ->leftJoin(DB::raw("(SELECT reseller_business_id, SUM(amount) AS adjustment FROM reseller_business_payments WHERE date < '$from' AND type='Adjustment' AND reseller_business_id = $request->reseller_business GROUP BY reseller_business_id) AS E"), function($q) {
                    $q->on('E.reseller_business_id', '=', 'users.id');
                })
                ->where('id',$request->reseller_business)
                ->where('role','Reseller Business');
                $openingBalance = $sql->first();
        // Report Lists
        $query1 = "SELECT `code`,`date`,'Sale' AS type,note, 'admin.sale.sales.show' AS route, id,  total_amount AS amount FROM sales AS X WHERE reseller_business_id = $request->reseller_business $dateCond";
        $query2 = "SELECT `code`,`date`,'Sale Return' AS type,note, 'admin.sale.return.show' AS route, id, return_amount AS amount FROM sale_returns AS X WHERE reseller_business_id = $request->reseller_business $dateCond";
        $query3 = "SELECT `receipt_no` AS code,`date`,'Received' AS type,note, 'admin.payment.reseller-business-payment.show' AS route, id, amount FROM reseller_business_payments AS X WHERE type='Received' AND reseller_business_id = $request->reseller_business $dateCond";
        $query4 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.reseller-business-payment.show' AS route, id, amount FROM reseller_business_payments AS X WHERE type='Payment' AND reseller_business_id = $request->reseller_business $dateCond";
        $query5 = "SELECT `receipt_no` AS code,`date`,'Adjustment' AS type,note, 'admin.payment.reseller-business-payment.show' AS route, id, amount FROM reseller_business_payments AS X WHERE type='Adjustment' AND reseller_business_id = $request->reseller_business $dateCond";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5) S ORDER BY S.`date` ASC");
        if ($request['action'] == 'print') {
            $title = 'Reseller Business Ladger Details';
            return view('admin.report.print.reseller-business-transactions', compact('data', 'reports','openingBalance','title'));
        }
        return view('admin.report.reseller-business-transactions', compact('data', 'reports', 'reseller_businesss','openingBalance'));
    }
    public function supplierTransactions(Request $request)
    {
        $suppliers = Supplier::select('id','name')->where('status','Active')->get();
        if ($request->supplier == null) {
            return view('admin.report.supplier-transactions', compact('suppliers'));
        }

        $data = Supplier::find($request->supplier);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.supplier', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        // Opening Balance
        $sql = Supplier::select(DB::raw("
                    (
                        (IFNULL(suppliers.opening_due, 0) 
                        + IFNULL(A.purchase_amount, 0) 
                        + IFNULL(F.accessory_purchase_amount, 0) 
                        + IFNULL(C.received, 0)
                    ) 
                        - 
                    (
                        IFNULL(B.return_amount, 0) 
                        + IFNULL(G.accessory_return_amount, 0) 
                        + IFNULL(D.payment, 0) 
                        + IFNULL(E.adjustment, 0)
                    )
                    ) AS balance
                "))
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS purchase_amount FROM purchases WHERE date < '$from' AND supplier_id = $request->supplier GROUP BY supplier_id) AS A"), function($q) {
                    $q->on('A.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS return_amount FROM purchase_returns WHERE date < '$from' AND supplier_id = $request->supplier GROUP BY supplier_id) AS B"), function($q) {
                    $q->on('B.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS received FROM supplier_payments WHERE date < '$from' AND type='Received' AND supplier_id = $request->supplier GROUP BY supplier_id) AS C"), function($q) {
                    $q->on('C.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS payment FROM supplier_payments WHERE date < '$from' AND type='Payment' AND supplier_id = $request->supplier GROUP BY supplier_id) AS D"), function($q) {
                    $q->on('D.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS adjustment FROM supplier_payments WHERE date < '$from' AND type='Adjustment' AND supplier_id = $request->supplier GROUP BY supplier_id) AS E"), function($q) {
                    $q->on('E.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS accessory_purchase_amount FROM accessory_stocks WHERE date < '$from' AND supplier_id = $request->supplier GROUP BY supplier_id) AS F"), function($q) {
                    $q->on('F.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw("(SELECT supplier_id, SUM(subtotal_amount) AS accessory_return_amount FROM accessory_stock_returns WHERE date < '$from' AND supplier_id = $request->supplier GROUP BY supplier_id) AS G"), function($q) {
                    $q->on('G.supplier_id', '=', 'suppliers.id');
                })
                ->where('id',$request->supplier);
            $openingBalance = $sql->first();

        // Report Lists
        $query1 = "SELECT `code`,`date`,'Stock' AS type,note, 'admin.purchase.raw.show' AS route, id, subtotal_amount AS amount FROM purchases AS X WHERE supplier_id = $request->supplier $dateCond";
        $query2 = "SELECT `code`,`date`,'Return' AS type,note, 'admin.purchase.return.raw.show' AS route, id, subtotal_amount AS amount FROM purchase_returns AS X WHERE supplier_id = $request->supplier $dateCond";
        $query3 = "SELECT `receipt_no` AS code,`date`,'Received' AS type,note, 'admin.payment.supplier-payments.show' AS route, id, total_amount FROM supplier_payments AS X WHERE type='Received' AND supplier_id = $request->supplier $dateCond";
        $query4 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.supplier-payments.show' AS route, id, total_amount FROM supplier_payments AS X WHERE type='Payment' AND supplier_id = $request->supplier $dateCond";
        $query5 = "SELECT `receipt_no` AS code,`date`,'Adjustment' AS type,note, 'admin.payment.supplier-payments.show' AS route, id, total_amount FROM supplier_payments AS X WHERE type='Adjustment' AND supplier_id = $request->supplier $dateCond";
        $query6 = "SELECT `code`,`date`,'Accessory Stock' AS type,note, 'admin.accessory.purchase.show' AS route, id, subtotal_amount AS amount FROM accessory_stocks AS X WHERE supplier_id = $request->supplier $dateCond";
        $query7 = "SELECT `code`,`date`,'Accessory Return' AS type,note, 'admin.accessory.purchase_returns.show' AS route, id, subtotal_amount AS amount FROM accessory_stock_returns AS X WHERE supplier_id = $request->supplier $dateCond";
        
        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5 UNION ALL $query6 UNION ALL $query7) S ORDER BY S.`date` ASC");
        if ($request['action'] == 'print') {
            $title = 'Supplier Ladger Details';
            return view('admin.report.print.supplier-transactions', compact('data', 'reports', 'suppliers','openingBalance','title'));
        }
        return view('admin.report.supplier-transactions', compact('data', 'reports', 'suppliers','openingBalance'));
    }
    public function resellerTransactions(Request $request)
    {
        $users = User::where('role','Reseller')->get(['id','name']);

        if ($request->reseller == null) {
            return view('admin.report.reseller-transactions', compact('users'));
        }

        $data = User::find($request->reseller);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.reseller', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $from = $request->from;
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($from)."'";
        }
        if ($request->to) {
            $to = $request->to;
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($to)."'";
        }
        // Report Lists
        $query1 = "SELECT `code`,`date`,'Orders' AS type,note, 'admin.orders.show' AS route, id, IFNULL(amount, 0) AS amount FROM orders AS X WHERE user_id = $request->reseller $dateCond";
        $query2 = "SELECT `code`,`date`,'Sales' AS type,note, 'admin.sale.sales.show' AS route, id, IFNULL(total_amount, 0) AS amount FROM sales AS X WHERE user_id = $request->reseller $dateCond";
        $query3 = "SELECT `code`,`date`,'Sale Returns' AS type,note, 'admin.sale.return.show' AS route, id, IFNULL(return_amount, 0) AS amount FROM sale_returns AS X WHERE user_id = $request->reseller $dateCond";
        $query4 = "SELECT `X`.`receipt_no` AS code,`X`.`date` AS date,'Received' AS type,`X`.`note` AS note,'admin.payment.customer-payments.show' AS route, `X`.`id`, `X`.`amount` AS amount FROM `customer_payments` AS X JOIN `orders` ON `X`.`order_id` = `orders`.`id` WHERE `X`.`type` = 'Received' AND `orders`.`user_id` = $request->reseller $dateCond";
        $query5 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.reseller-payments.show' AS route, id, total_amount AS amount FROM reseller_payments AS X WHERE type='Payment' AND reseller_id = $request->reseller $dateCond";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5) S ORDER BY S.`date` ASC");
       
        if ($request['action'] == 'print') {
            $title = 'Reseller Ladger Details';
            return view('admin.report.print.reseller-transactions', compact('data', 'reports', 'users','title'));
        }
        return view('admin.report.reseller-transactions', compact('data', 'reports', 'users'));
    }
    public function sellerTransactions(Request $request)
    {
        $users = User::where('role','Seller')->get(['id','name']);

        if ($request->seller == null) {
            return view('admin.report.seller-transactions', compact('users'));
        }

        $data = User::find($request->seller);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.seller', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $from = $request->from;
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($from)."'";
        }
        if ($request->to) {
            $to = $request->to;
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($to)."'";
        }
        // Report Lists
        $query1 = "SELECT `code`,`date`,'Orders' AS type,note, 'admin.orders.show' AS route, id, IFNULL(amount, 0) AS amount FROM orders AS X WHERE user_id = $request->seller $dateCond";
        $query2 = "SELECT `code`,`date`,'Sales' AS type,note, 'admin.sale.sales.show' AS route, id, IFNULL(total_amount, 0) AS amount FROM sales AS X WHERE user_id = $request->seller $dateCond";
        $query3 = "SELECT `code`,`date`,'Sale Returns' AS type,note, 'admin.sale.return.show' AS route, id, IFNULL(return_amount, 0) AS amount FROM sale_returns AS X WHERE user_id = $request->seller $dateCond";
        $query4 = "SELECT `X`.`receipt_no` AS code,`X`.`date` AS date,'Received' AS type,`X`.`note` AS note,'admin.payment.customer-payments.show' AS route, `X`.`id`, `X`.`amount` AS amount FROM `customer_payments` AS X JOIN `orders` ON `X`.`order_id` = `orders`.`id` WHERE `X`.`type` = 'Received' AND `orders`.`user_id` = $request->seller $dateCond";
        $query5 = "SELECT `X`.`receipt_no` AS code,`X`.`date` AS date,'Payment' AS type,`X`.`note` AS note,'admin.payment.customer-payments.show' AS route, `X`.`id`, `X`.`amount` AS amount FROM `customer_payments` AS X JOIN `orders` ON `X`.`order_id` = `orders`.`id` WHERE `X`.`type` = 'Payment' AND `orders`.`user_id` = $request->seller $dateCond";
        $query6 = "SELECT `X`.`receipt_no` AS code,`X`.`date` AS date,'Adjustment' AS type,`X`.`note` AS note,'admin.payment.customer-payments.show' AS route, `X`.`id`, `X`.`amount` AS amount FROM `customer_payments` AS X JOIN `orders` ON `X`.`order_id` = `orders`.`id` WHERE `X`.`type` = 'Adjustment' AND `orders`.`user_id` = $request->seller $dateCond";
        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5 UNION ALL $query6) S ORDER BY S.`date` ASC");
        if ($request['action'] == 'print') {
            $title = 'Seller Ladger Details';
            return view('admin.report.print.seller-transactions', compact('data', 'reports', 'users','title'));
        }
        return view('admin.report.seller-transactions', compact('data', 'reports', 'users'));
    }


    public function deliveryAgentTransactions(Request $request)
    {
        $delivery_agents = DeliveryAgent::where('status','Active')->get();

        if ($request->delivery_agent == null) {
            return view('admin.report.delivery-agent-transactions', compact('delivery_agents'));
        }

        $data = DeliveryAgent::find($request->delivery_agent);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.delivery-agent', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        // Opening Balance
        $sql = DeliveryAgent::select(DB::raw("((IFNULL(delivery_agents.opening_due, 0) + IFNULL(A.shipping_charge_amount, 0)  + IFNULL(A.extraShipping_charge_amount, 0) + IFNULL(C.received, 0)) - (IFNULL(D.payment, 0) + IFNULL(E.adjustment, 0))) AS balance"))
                ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(shipping_charge) AS shipping_charge_amount , SUM(extra_shipping_charge) AS extraShipping_charge_amount FROM sales WHERE date < '$from' AND delivery_agent_id = $request->delivery_agent GROUP BY delivery_agent_id) AS A"), function($q) {
                    $q->on('A.delivery_agent_id', '=', 'delivery_agents.id');
                })
                ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS received FROM delivery_agent_payments WHERE date < '$from' AND type='Received' AND delivery_agent_id = $request->delivery_agent GROUP BY delivery_agent_id) AS C"), function($q) {
                    $q->on('C.delivery_agent_id', '=', 'delivery_agents.id');
                })
                ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS payment FROM delivery_agent_payments WHERE date < '$from' AND type='Payment' AND delivery_agent_id = $request->delivery_agent GROUP BY delivery_agent_id) AS D"), function($q) {
                    $q->on('D.delivery_agent_id', '=', 'delivery_agents.id');
                })
                ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS adjustment FROM delivery_agent_payments WHERE date < '$from' AND type='Adjustment' AND delivery_agent_id = $request->delivery_agent GROUP BY delivery_agent_id) AS E"), function($q) {
                    $q->on('E.delivery_agent_id', '=', 'delivery_agents.id');
                })
                ->where('id',$request->delivery_agent);
            $openingBalance = $sql->first();
            // Report Lists
            $query1 = "SELECT `code`,`date`,'shippingChargeAmount' AS type,note, 'admin.sale.sales.show' AS route, id, (shipping_charge + extra_shipping_charge) AS amount FROM sales AS X WHERE delivery_agent_id = $request->delivery_agent $dateCond";
            $query2 = "SELECT `receipt_no` AS code,`date`,'Received' AS type,note, 'admin.payment.delivery-agent-payments.show' AS route, id, total_amount FROM delivery_agent_payments AS X WHERE type='Received' AND delivery_agent_id = $request->delivery_agent $dateCond";
            $query3 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.delivery-agent-payments.show' AS route, id, total_amount FROM delivery_agent_payments AS X WHERE type='Payment' AND delivery_agent_id = $request->delivery_agent $dateCond";
            $query4 = "SELECT `receipt_no` AS code,`date`,'Adjustment' AS type,note, 'admin.payment.delivery-agent-payments.show' AS route, id, total_amount FROM delivery_agent_payments AS X WHERE type='Adjustment' AND delivery_agent_id = $request->delivery_agent $dateCond";

            $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4) S ORDER BY S.`date` ASC");
            if ($request['action'] == 'print') {
                $title = 'Delivery Agent Ladger Details';
                return view('admin.report.print.delivery-agent-transactions', compact('data', 'reports', 'delivery_agents', 'openingBalance','title'));
            }
            return view('admin.report.delivery-agent-transactions', compact('data', 'reports', 'delivery_agents','openingBalance'));
    }

    public function rawStock(Request $request)
    {
        $reports = $this->rawMaterialStock($request);
        $type = 'raw-material';
        if ($request['action'] == 'print') {
            $title = 'Raw Product Stock Ladger';
            return view('admin.report.print.raw-product-stock', compact('reports','title'));
        }
        return view('admin.report.raw-product-stock', compact('reports','type'));
    }
    public function rawMaterialStock($request){

        $sql = Product::select([
            'products.code as product_code',
            'products.name as product_name',
            'products.stock_price',
            'products.id',
            'purchase_quantity',
            'dyeing_quantity',
            'production_quantity',
            'purchase_return_quantity',
            'damage_quantity',
            DB::raw(sprintf(
                '(%s - %s - %s - %s - %s) as stock_quantity',
                'ifnull(purchase_quantity, 0)',
                'ifnull(dyeing_quantity, 0)',
                'ifnull(production_quantity, 0)',
                'ifnull(purchase_return_quantity, 0)',
                'ifnull(damage_quantity, 0)',
            )),
            DB::raw(sprintf(
                '(%s - %s - %s - %s - %s) as stock_amount',
                'ifnull(purchase_quantity, 0) * products.stock_price',
                'ifnull(dyeing_quantity, 0) * products.stock_price',
                'ifnull(production_quantity, 0) * products.stock_price',
                'ifnull(purchase_return_quantity, 0) * products.stock_price',
                'ifnull(damage_quantity, 0) * products.stock_price',
            )),
        ])
            //->leftJoin('colors', 'product_ins.color_id', '=', 'colors.id')
            ->leftJoinSub(
                ProductIn::select([
                    'product_id',
                    DB::raw('sum(quantity) as purchase_quantity'),
                ])
                    //->where('product_ins.type', 'Purchase')
                    ->groupBy('product_id')
                , 'purchase_product_ins', function ($join) {
                $join->on('products.id', '=', 'purchase_product_ins.product_id');
                //$join->on('product_ins.color_id', '=', 'purchase_product_ins.color_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    DB::raw('sum(quantity) as production_quantity'),
                ])
                    ->where('type', 'Production')
                    ->groupBy('product_id')
                , 'production_product_outs', function ($join) {
                $join->on('products.id', '=', 'production_product_outs.product_id');
                //$join->on('product_ins.color_id', '=', 'production_product_outs.color_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    DB::raw('sum(quantity) as purchase_return_quantity'),
                ])
                    ->where('type', 'PurchaseReturn')
                    ->groupBy('product_id')
                , 'purchase_return_product_outs', function ($join) {
                $join->on('products.id', '=', 'purchase_return_product_outs.product_id');
                //$join->on('product_ins.color_id', '=', 'purchase_return_product_outs.color_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    //'color_id',
                    DB::raw('sum(quantity) as damage_quantity'),
                ])
                    ->where('type', 'Damage')
                    ->groupBy('product_id')
                , 'damage_product_outs', function ($join) {
                $join->on('products.id', '=', 'damage_product_outs.product_id');
                //$join->on('product_ins.color_id', '=', 'damage_product_outs.color_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    //'color_id',
                    DB::raw('sum(quantity) as dyeing_quantity'),
                ])
                    ->where('type', 'Dyeing')
                    ->groupBy('product_id')
                , 'dyeing_product_outs', function ($join) {
                $join->on('products.id', '=', 'dyeing_product_outs.product_id');
                //$join->on('product_ins.color_id', '=', 'damage_product_outs.color_id');
            })
            ->groupBy('products.id')
            ->whereIn('products.product_type', ['Fabric', 'Grey'])
            ->where('products.status', 'Active');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('products.code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('products.name', 'LIKE', '%' . $request->q . '%');
            });
        }
        return $sql->get();
    }
    public function ledger(Request $request)
    {
        $products = Product::select('id','name')->where('status', 'Active')->whereIn('product_type',['Fabric', 'Grey'])->get();
        //$colors = Color::select('id','name')->where('status', 'Active')->get();
        if ($request->product_id == null) {
            return view('admin.report.raw-product-ledger', compact('products'));
        }
        $product = Product::find($request->product_id);
        //$color = $request->color_id ?? '' ;
        $asOnDate = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $asOnDate .= "AND DATE(created_at) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $asOnDate .= "AND DATE(created_at) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }
        $query1 = "SELECT 'Purchase' AS type, 'admin.purchase.raw.show' AS route, flagable_id AS rowId, created_at, quantity,(used_quantity) as usedQuantity,unit_price as amount,actual_unit_price FROM product_ins WHERE product_id = $request->product_id AND type = 'Purchase' $asOnDate";
        $query2 = "SELECT 'FromDyeing' AS type, 'admin.receive-dyeing.show' AS route, flagable_id AS rowId, created_at, quantity,(used_quantity) as usedQuantity, unit_price as amount, actual_unit_price FROM product_ins WHERE product_id = $request->product_id AND type = 'Dyeing' $asOnDate";
        $query3 = "SELECT 'PurchaseReturn' AS type, 'admin.purchase.return.raw.show' AS route, flagable_id AS rowId, created_at, quantity,'0' as usedQuantity,unit_price as amount,'0' as actual_unit_price FROM product_outs WHERE product_id = $request->product_id AND type = 'PurchaseReturn' $asOnDate";
        $query4 = "SELECT 'Production' AS type, 'admin.production.show' AS route, flagable_id AS rowId, created_at, quantity,'0' as usedQuantity,unit_price as amount,'0' as actual_unit_price FROM product_outs WHERE product_id = $request->product_id AND type = 'Production' $asOnDate";
        $query5 = "SELECT 'ToDyeing' AS type, 'admin.send-dyeing.show' AS route, flagable_id AS rowId, created_at, quantity,'0' as usedQuantity,unit_price as amount,'0' as actual_unit_price FROM product_outs WHERE product_id = $request->product_id AND type = 'Dyeing' $asOnDate";
        $query6 = "SELECT 'Damage' AS type, 'admin.damage.raw.show' AS route, flagable_id AS rowId, created_at, quantity,'0' as usedQuantity,unit_price as amount,'0' as actual_unit_price FROM product_outs WHERE product_id = $request->product_id AND type = 'Damage' $asOnDate";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5 UNION ALL $query6) S ORDER BY S.`created_at` ASC");
        if ($request['action'] == 'print') {
            $title = 'Raw Product Stock Details Ladger';
            return view('admin.report.print.raw-product-ledger', compact('reports', 'product','title'));
        }
        return view('admin.report.raw-product-ledger', compact('reports', 'product','products'));
    }

    public function finishedLedger(Request $request)
    {
        $products = Product::select('id','name')->where('status', 'Active')->whereIn('product_type', ['Base', 'Product'])->get();
        if ($request->product_id == null) {
            return view('admin.report.finished-product-ledger', compact('products'));
        }

        $product = Product::find($request->product_id);

        $asOnDate = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $asOnDate .= "AND DATE(created_at) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $asOnDate .= "AND DATE(created_at) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        $query1 = "SELECT 'Production' AS type, 'admin.production.show' AS route, flagable_id AS rowId, created_at, quantity, actual_unit_price as unit_price, used_quantity FROM product_ins WHERE product_id = $request->product_id AND type = 'Production' $asOnDate";
        $query3 = "SELECT 'Sale Return' AS type, 'admin.sale.return.show' AS route, flagable_id AS rowId, created_at, quantity, actual_unit_price as unit_price, used_quantity FROM product_ins WHERE product_id = $request->product_id AND type = 'SaleReturn' $asOnDate";
        $query5 = "SELECT 'Purchase' AS type, 'admin.purchase.finished.show' AS route, flagable_id AS rowId, created_at, quantity, actual_unit_price as unit_price, used_quantity FROM product_ins WHERE product_id = $request->product_id AND type = 'Purchase' $asOnDate";
        $query2 = "SELECT 'Sale' AS type, 'admin.sale.sales.show' AS route, flagable_id AS rowId, created_at, quantity, net_unit_price, 0 as used_quantity FROM product_outs WHERE product_id = $request->product_id AND type = 'Sale' $asOnDate";
        $query4 = "SELECT 'Damage' AS type, 'admin.damage.finished.show' AS route, flagable_id AS rowId, created_at, quantity, net_unit_price, 0 as used_quantity FROM product_outs WHERE product_id = $request->product_id AND type = 'Damage' $asOnDate";
        $query6 = "SELECT 'Purchase Return' AS type, 'admin.purchase.return.finished.show' AS route, flagable_id AS rowId, created_at, quantity, net_unit_price, 0 as used_quantity FROM product_outs WHERE product_id = $request->product_id AND type = 'PurchaseReturn' $asOnDate";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5 UNION ALL $query6) S ORDER BY S.`created_at` ASC");

        if ($request['action'] == 'print') {
            $title = 'Finished Product Stock Details Ladger';
            return view('admin.report.print.finished-product-ledger', compact('reports', 'product', 'title'));
        }
        return view('admin.report.finished-product-ledger', compact('reports', 'product','products'));
    }

    public function finishedStock(Request $request,$type)
    {
        $reports = $this->finishedStockList($request);
        if ($request['action'] == 'print') {
            $title = 'Finished Product Ladger';
            return view('admin.report.print.finished-product-stock', compact('reports','title'));
        }
        return view('admin.report.finished-product-stock', compact('reports','type'));
    }

    public function finishedStockList($request)
    {
        $sql = Product::select([
            'products.code as product_code',
            'products.name as product_name',
            'products.id',
            'production_quantity',
            'purchase_quantity',
            'sale_return_quantity',
            'sale_quantity',
            'purchase_return_quantity',
            'damage_quantity',
            DB::raw(sprintf(
                '(%s + %s + %s - %s - %s - %s) as stock_quantity',
                'ifnull(production_quantity, 0)',
                'ifnull(purchase_quantity, 0)',
                'ifnull(sale_return_quantity, 0)',
                'ifnull(sale_quantity, 0)',
                'ifnull(purchase_return_quantity, 0)',
                'ifnull(damage_quantity, 0)',
            )),
            DB::raw(sprintf(
                '(%s + %s + %s - %s - %s - %s) as stock_amount',
                'ifnull(production_quantity, 0) * products.stock_price',
                'ifnull(purchase_quantity, 0) * products.stock_price',
                'ifnull(sale_return_quantity, 0) * products.stock_price',
                'ifnull(sale_quantity, 0) * products.stock_price',
                'ifnull(purchase_return_quantity, 0) * products.stock_price',
                'ifnull(damage_quantity, 0) * products.stock_price',
            )),
        ])
            
            ->leftJoinSub(
                ProductIn::select([
                    'product_id',
                    DB::raw('sum(quantity) as production_quantity'),
                    DB::raw('sum(quantity * actual_unit_price) as production_amount'),
                ])
                    ->where('type', 'Production')
                    ->groupBy('product_id')
                , 'production_product_ins', function ($join) {
                $join->on('products.id', '=', 'production_product_ins.product_id');
            })
            ->leftJoinSub(
                ProductIn::select([
                    'product_id',
                    DB::raw('sum(quantity) as purchase_quantity'),
                ])
                    ->where('type', 'Purchase')
                    ->groupBy('product_id')
                , 'purchase_product_ins', function ($join) {
                $join->on('products.id', '=', 'purchase_product_ins.product_id');
            })
            ->leftJoinSub(
                ProductIn::select([
                    'product_id',
                    DB::raw('sum(quantity) as sale_return_quantity'),
                ])
                    ->where('type', 'SaleReturn')
                    ->groupBy('product_id')
                , 'sale_return_product_ins', function ($join) {
                $join->on('products.id', '=', 'sale_return_product_ins.product_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    DB::raw('sum(quantity) as sale_quantity'),
                ])
                    ->where('type', 'Sale')
                    ->groupBy('product_id')
                , 'sale_product_outs', function ($join) {
                $join->on('products.id', '=', 'sale_product_outs.product_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    DB::raw('sum(quantity) as purchase_return_quantity'),
                ])
                    ->where('type', 'PurchaseReturn')
                    ->groupBy('product_id')
                , 'purchase_return_product_outs', function ($join) {
                $join->on('products.id', '=', 'purchase_return_product_outs.product_id');
            })
            ->leftJoinSub(
                ProductOut::select([
                    'product_id',
                    DB::raw('sum(quantity) as damage_quantity'),
                ])
                    ->where('type', 'Damage')
                    ->groupBy('product_id')
                , 'damage_product_outs', function ($join) {
                $join->on('products.id', '=', 'damage_product_outs.product_id');
            })
            ->groupBy('products.id')
            ->whereIn('products.product_type', ['Base', 'Product'])
            ->where('products.status', 'Active');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('products.code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('products.name', 'LIKE', '%' . $request->q . '%');
            });
        }
        return $sql->get();
    }

    public function incomeStatement(Request $request)
    {
        $this->authorize('profit-loss report');

        $data['salesAmount'] = Sale::where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })->where('status','Delivered')->sum('total_amount');

        $data['saleReturnsAmount'] = SaleReturn::where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })->sum('return_amount');

        $data['rawStockAmount'] = Purchase::where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })->where('type', 'Raw')->sum('total_amount');

        $data['finishedStockAmount'] = Purchase::where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })->where('type', 'Finished')->sum('total_amount');

        $data['rawStockReturnsAmount'] = PurchaseReturn::where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })->where('type', 'Raw')->sum('total_amount');

        $data['finishedStockReturnsAmount'] = PurchaseReturn::where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })->where('type', 'Finished')->sum('total_amount');

        // $stockCost = ProductIn::select(DB::raw('SUM(product_ins.cost) AS amount'))
        // ->join('purchases', 'purchases.id', '=', 'product_ins.flagable_id')
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->where('purchases.date', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->where('purchases.date', '<=', $request->to);
        //     }
        // })
        // ->where('type','Purchase')
        // ->first();
        // $data['stockCost'] = $stockCost->amount ?? 0;

        // $stockReturnCost = ProductOut::select(DB::raw('SUM(product_outs.cost) AS amount'))
        // ->join('purchase_returns', 'purchase_returns.id', '=', 'product_ins.flagable_id')
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->where('purchase_returns.date', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->where('purchase_returns.date', '<=', $request->to);
        //     }
        // })
        // ->where('type','PurchaseReturn')
        // ->first();
        // $data['stockReturnCost'] = $stockReturnCost->amount ?? 0;

        // $stockReturnCost = StockReturnItemBarcode::select(DB::raw('SUM(stock_return_item_barcodes.purchase_net_price) AS amount'))
        // ->join('stock_return_items', 'stock_return_items.id', '=', 'stock_return_item_barcodes.stock_return_item_id')
        // ->join('stock_returns', 'stock_returns.id', '=', 'stock_return_items.stock_return_id')
        // ->where('stock_returns.branch_id', Auth::user()->branch_id)
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->where('stock_returns.return_date', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->where('stock_returns.return_date', '<=', $request->to);
        //     }
        // })
        // ->whereNotNull('stock_returns.approved_at')
        // ->first();
        // $data['stockReturnCost'] = $stockReturnCost->amount ?? 0;

        // Sales Return item er Purchase price
        // $saleReturnCost = ProductIn::select(DB::raw('SUM(product_ins.actual_unit_price * product_ins.quantity) AS amount'))
        // ->join('purchases', function($q) {
        //     $q->on('purchases.id', '=', 'product_ins.flagable_id');
        //     $q->where('type', 'SaleReturn');
        // })
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->where('purchases.date', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->where('purchases.date', '<=', $request->to);
        //     }
        // })
        // ->first();
        // $data['saleReturnCost'] = $saleReturnCost->amount ?? 0;


        $data['expenses'] = Expense::with('category')
        ->select('category_id', DB::raw("SUM(amount) AS amount"))
        ->where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })
        ->whereNotNull('approved_at')
        ->groupBy('category_id')
        ->get();

        $data['incomes'] = Income::with('category')
        ->select('category_id', DB::raw("SUM(amount) AS amount"))
        ->where(function($q) use($request) {
            if ($request->from) {
                $q->where('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->where('date', '<=', $request->to);
            }
        })
        ->whereNotNull('approved_at')
        ->groupBy('category_id')
        ->get();

        // $data['customerAdj'] = CustomerPayment::where('type', 'Adjustment')
        // ->select(DB::raw("SUM(amount) AS amount"))
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->where('date', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->where('date', '<=', $request->to);
        //     }
        // })
        // ->whereNotNull('approved_at')
        // ->get();

        // $data['supplierAdj'] = SupplierPayment::where('type', 'Adjustment')
        // ->select(DB::raw("SUM(amount) AS amount"))
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->where('date', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->where('date', '<=', $request->to);
        //     }
        // })
        // ->whereNotNull('approved_at')
        // ->get();

        // $data['bankPaymentCost'] = Transaction::where('type', 'Payment')
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->whereDate('datetime', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->whereDate('datetime', '<=', $request->to);
        //     }
        // })
        // ->whereNotNull('approved_at')
        // ->sum('cost');

        // $data['bankReceivedCost'] = Transaction::where('type', 'Received')
        // ->where(function($q) use($request) {
        //     if ($request->from) {
        //         $q->whereDate('datetime', '>=', $request->from);
        //     }
        //     if ($request->to) {
        //         $q->whereDate('datetime', '<=', $request->to);
        //     }
        // })
        // ->whereNotNull('approved_at')
        // ->sum('cost');

        if ($request['action'] == 'print') {
            $data['title'] = 'Profit Loss Ladger';
            return view('admin.report.profit-loss-ledger-print', $data);
        }
        return view('admin.report.profit-loss', $data);
    }

    public function trialBalance(Request $request)
    {
        $request->date = $request->date;
        $request->to = $request->date;

        $data['closingRawMaterial'] = $this->rawMaterialStock($request)->sum('stockQtyPrice');
        $data['closingFinished'] = $this->finishedStockList($request,'finished')->sum('stockQtyPrice');
        $data['supplierClosingBalance'] = $this->totalSupplierDue($request)->sum('dueAmount');
        $data['customerClosingBalance'] = $this->totalCustomerDue($request)->sum('dueAmount');
        $data['expense'] = $this->total_expense_due($request)->sum('amount');
        $data['bankCash'] = $this->bankList($request)->sum('balanceAmount');
        $data['incomeBalance'] = $this->incomeList($request)->sum('amount');
        $data['profitLossAmount'] = $this->profitLossAmount($request);
        return view('admin.report.trial-balance', $data);
    }
    public function profitLossAmount(Request $request)
    {
        if($request->date){
            $from = $request->date;
        } else {
            $from = $request->date = '1970-01-01';
        }

        if($request->to){
            $to = $request->to;
        } else {
            $to = $request->to = date('Y-m-d');
        }



        // Sales
        $sale = Sale::orderBy('id', 'DESC');
        if($from != null){
            $sale->where('date', '>=', $from);
        }
        if($to != null){
            $sale->where('date', '<=', $to);
        }
        $data['salesResellerAmount'] = $sale->sum('reseller_amount');
        $data['salesSellerCommisionAmount'] = $sale->sum('commission_amount');
        $data['salesAmount'] = $sale->sum('total_amount');

        // Sales Return
        $saleReturn = SaleReturn::orderBy('id', 'DESC');
        if($from != null){
            $saleReturn->where('date', '>=', $from);
        }
        if($to != null){
            $saleReturn->where('date', '<=', $to);
        }
        $data['salesReturnResellerAmount'] = $saleReturn->sum('reseller_amount');
        $data['saleReturnSellerCommissionAmount'] = $saleReturn->sum('deduction_amount');
        $data['saleReturnAmount'] = $saleReturn->sum('return_amount');

        // finished Opening And Closing

        $data['finishedOpeningStockPrice'] = $this->finishedStockList($request, $from,'finished','trial-balance')->sum('stockQtyPrice');

        $data['finishedClosingStockPrice'] = $this->finishedStockList($request, $to,'finished','trial-balance')->sum('stockQtyPrice');


        // Production
        $production = Production::with('raw_items');
        if($from != null){
            $production->where('date', '>=', $from);
        }
        if($to != null){
            $production->where('date', '<=', $to);
        }
        $data['productionAmount'] = $production->get()->flatMap(function ($production) {
            return $production->raw_items;
        })->sum('total_price');

        // Expense
        $data['expenseAmount'] = $this->total_expense_due($request)->sum('amount');

        // Customer Adjustment
        $customerAdjustment = CustomerPayment::where('type','Adjustment');
        if($from != null){
            $customerAdjustment->where('date', '>=', $from);
        }
        if($to != null){
            $customerAdjustment->where('date', '<=', $to);
        }
        $data['customerAdjustment'] = $customerAdjustment->sum('amount');



        // Income
        $data['incomeAmount'] = $this->incomeList($request)->sum('amount');

        // Supplier Adjustment
        $supplierAdjustment = SupplierPayment::where('type','Adjustment');
        if($from != null){
            $supplierAdjustment->where('date', '>=', $from);
        }
        if($to != null){
            $supplierAdjustment->where('date', '<=', $to);
        }
        $data['supplierAdjustment'] = $supplierAdjustment->sum('total_amount');





        // Sales Reseller Adjustment
        $salesReferenceAdjustment = ResellerPayment::where('type','Adjustment');
        if($from != null){
            $salesReferenceAdjustment->where('date', '>=', $from);
        }
        if($to != null){
            $salesReferenceAdjustment->where('date', '<=', $to);
        }
        $data['salesReferenceAdjustment'] = $salesReferenceAdjustment->sum('total_amount');


        // Sales Seller Comission Adjustment
        $salesComissionAdjustment = SellerCommission::where('type','Adjustment');
        if($from != null){
            $salesComissionAdjustment->where('date', '>=', $from);
        }
        if($to != null){
            $salesComissionAdjustment->where('date', '<=', $to);
        }
        $data['salesComissionAdjustment'] = $salesComissionAdjustment->sum('total_amount');


        // Final Calculation on Above Results
        // Gross Profit
        $data['grossProfit'] = ($data['salesAmount'] - $data['saleReturnAmount']) - ($data['finishedOpeningStockPrice'] + $data['productionAmount'] - $data['finishedClosingStockPrice']);
        // TotalExpense
        $data['totalExpense'] = ($data['expenseAmount']  + $data['salesSellerCommisionAmount'] - $data['saleReturnSellerCommissionAmount'] + $data['salesResellerAmount'] - $data['salesReturnResellerAmount']);
        // TotalIncome
        $data['totalIncome'] = $data['incomeAmount'] ;//+ $data['supplierAdjustment'] + $data['laborAdjustment'] + $data['transprotAdjustment'] + $data['salesReferenceAdjustment'] + $data['salesComissionAdjustment'] + $data['loanHolderAdjustment'];
        // FinalIncome
        $data['finalBalance'] = $data['grossProfit'] + $data['totalIncome'];
        // netProfit
        $data['netProfit'] = $data['finalBalance'] - $data['totalExpense'];
        //dd($data);

        return $data;
    }


    //Dyeing Agent

    public function dyeingAgent(Request $request)
    {
        $reports = $this->totalDyeingAgentDue($request);
        if ($request['action'] == 'print') {
            $title = 'Dyeing Agent Ladger';
            return view('admin.report.print.dyeing-agent', compact('reports','title'));
        }
        return view('admin.report.dyeing-agent', compact('reports'));
    }
    public function totalDyeingAgentDue($request){
        $grn = $purchaseReturn = $payment = '';
        if($request->date <> ''){
            $grn .= ' WHERE date <= "'.$request->date.'"';
        }
        if($request->date <> ''){
            $purchaseReturn .= ' WHERE date <= "'.$request->date.'"';
        }
        if($request->date <> ''){
            $payment .= ' AND date <= "'.$request->date.'"';
        }

        $sql = DyeingAgent::select(
            'dyeing_agents.*',
            DB::raw('IFNULL(A.total_cost, 0) AS total_cost'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),
            DB::raw('
                        (
                            
                                (
                                    IFNULL(A.total_cost, 0) +
                                    IFNULL(C.receivedAmount, 0)
                                ) -
                                (
                                    IFNULL(D.paidAmount, 0) +
                                    IFNULL(E.adjustmentAmount, 0)
                                )
                        ) AS dueAmount
                    ')
        )
        ->orderBy('name', 'ASC');

        $sql->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_cost) AS total_cost FROM `receive_dyeings` $grn GROUP BY dyeing_agent_id) AS A"), function($q) {
            $q->on('A.dyeing_agent_id', '=', 'dyeing_agents.id');
        });

        $sql->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS receivedAmount FROM `dyeing_payments` WHERE type='Received' $payment GROUP BY dyeing_agent_id) AS C"), function($q) {
            $q->on('C.dyeing_agent_id', '=', 'dyeing_agents.id');
        });
        $sql->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS paidAmount FROM `dyeing_payments` WHERE type='Payment' $payment GROUP BY dyeing_agent_id) AS D"), function($q) {
            $q->on('D.dyeing_agent_id', '=', 'dyeing_agents.id');
        });
        $sql->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS adjustmentAmount FROM `dyeing_payments` WHERE type='Adjustment' $payment GROUP BY dyeing_agent_id) AS E"), function($q) {
            $q->on('E.dyeing_agent_id', '=', 'dyeing_agents.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('dyeing_agents.name', 'LIKE', $request->q.'%')
                ->orWhere('dyeing_agents.code', 'LIKE', $request->q.'%')
                ->orWhere('dyeing_agents.mobile', 'LIKE', $request->q.'%');
            });
        }

        $reports = $sql->get();

        return $reports;
    }

    public function dyeingAgentTransactions(Request $request)
    {
        $dyeing_agents = DyeingAgent::where('status','Active')->get();

        if ($request->dyeing_agent == null) {
            return view('admin.report.dyeing-agent-transactions', compact('dyeing_agents'));
        }

        $data = DyeingAgent::find($request->dyeing_agent);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.report.dyeing-agent', qArray());
        }

        $dateCond = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }

        // Opening Balance
        $sql = DyeingAgent::select(DB::raw("((IFNULL(A.total_cost, 0) + IFNULL(C.received, 0)) - (IFNULL(D.payment, 0) + IFNULL(E.adjustment, 0))) AS balance"))
                ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_cost) AS total_cost FROM receive_dyeings WHERE date < '$from' AND dyeing_agent_id = $request->dyeing_agent GROUP BY dyeing_agent_id) AS A"), function($q) {
                    $q->on('A.dyeing_agent_id', '=', 'dyeing_agents.id');
                })
                ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS received FROM dyeing_payments WHERE date < '$from' AND type='Received' AND dyeing_agent_id = $request->dyeing_agent GROUP BY dyeing_agent_id) AS C"), function($q) {
                    $q->on('C.dyeing_agent_id', '=', 'dyeing_agents.id');
                })
                ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS payment FROM dyeing_payments WHERE date < '$from' AND type='Payment' AND dyeing_agent_id = $request->dyeing_agent GROUP BY dyeing_agent_id) AS D"), function($q) {
                    $q->on('D.dyeing_agent_id', '=', 'dyeing_agents.id');
                })
                ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS adjustment FROM dyeing_payments WHERE date < '$from' AND type='Adjustment' AND dyeing_agent_id = $request->dyeing_agent GROUP BY dyeing_agent_id) AS E"), function($q) {
                    $q->on('E.dyeing_agent_id', '=', 'dyeing_agents.id');
                })
                ->where('id',$request->dyeing_agent);
        $openingBalance = $sql->first();
        // Report Lists
        $query1 = "SELECT `code`,`date`,'totalCost' AS type, note, 'admin.receive-dyeing.show' AS route, id, (total_cost) AS amount FROM receive_dyeings AS X WHERE dyeing_agent_id = $request->dyeing_agent $dateCond";
        $query2 = "SELECT `receipt_no` AS code,`date`,'Received' AS type,note, 'admin.payment.dyeing-payments.show' AS route, id, total_amount FROM dyeing_payments AS X WHERE type='Received' AND dyeing_agent_id = $request->dyeing_agent $dateCond";
        $query3 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.dyeing-payments.show' AS route, id, total_amount FROM dyeing_payments AS X WHERE type='Payment' AND dyeing_agent_id = $request->dyeing_agent $dateCond";
        $query4 = "SELECT `receipt_no` AS code,`date`,'Adjustment' AS type,note, 'admin.payment.dyeing-payments.show' AS route, id, total_amount FROM dyeing_payments AS X WHERE type='Adjustment' AND dyeing_agent_id = $request->dyeing_agent $dateCond";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4) S ORDER BY S.`date` ASC");
        if ($request['action'] == 'print') {
            $title = 'Dyeing Agent Ladger Details';
            return view('admin.report.print.dyeing-agent-transactions', compact('data', 'reports', 'dyeing_agents', 'openingBalance','title'));
        }
        return view('admin.report.dyeing-agent-transactions', compact('data', 'reports', 'dyeing_agents','openingBalance'));
    }
}
