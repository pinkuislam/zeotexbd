<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $sql = User::select(
            DB::raw('IFNULL(A.saleAmount, 0) AS saleAmount'),
            DB::raw('IFNULL(A.discountAmount, 0) AS discountAmount'),
            DB::raw('IFNULL(B.returnAmount, 0) AS returnAmount'),
            DB::raw('IFNULL(C.receivedAmount, 0) AS receivedAmount'),
            DB::raw('IFNULL(E.adjustmentAmount, 0) AS adjustmentAmount'),
            DB::raw('IFNULL(D.paidAmount, 0) AS paidAmount'),
            DB::raw('
                        (
                            IFNULL(users.opening_due, 0) +
                                (
                                    IFNULL(A.saleAmount, 0) +
                                    IFNULL(D.paidAmount, 0)
                                ) -
                                (
                                    IFNULL(A.discountAmount, 0) +
                                    IFNULL(B.returnAmount, 0) +
                                    IFNULL(C.receivedAmount, 0) +
                                    IFNULL(E.adjustmentAmount, 0)
                                )
                        ) AS dueAmount
                    ')
        );
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS saleAmount, SUM(discount_amount) as discountAmount FROM `sales` GROUP BY customer_id) AS A"), function($q) {
        $q->on('A.customer_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS returnAmount FROM `sale_returns` GROUP BY customer_id) AS B"), function($q) {
        $q->on('B.customer_id', '=', 'users.id');
        });

        $sql->leftJoin(DB::raw("(SELECT user_id, SUM(amount) AS receivedAmount FROM `payments` WHERE type='Received' GROUP BY user_id) AS C"), function($q) {
        $q->on('C.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(SELECT user_id, SUM(amount) AS paidAmount FROM `payments` WHERE type='Payment' GROUP BY user_id) AS D"), function($q) {
        $q->on('D.user_id', '=', 'users.id');
        });
        $sql->leftJoin(DB::raw("(SELECT user_id, SUM(amount) AS adjustmentAmount FROM `payments` WHERE type='Adjustment' GROUP BY user_id) AS E"), function($q) {
        $q->on('E.user_id', '=', 'users.id');
        });

        $sql->where('role','Customer');

        $totalCustomerDue = $sql->get();
        $data['total_customer_due'] = $totalCustomerDue->sum('dueAmount');
        dd($data);
        return view('admin.dashboard');
    }

    public function noAccess()
    {
        return view('admin.no-access');
    }
}