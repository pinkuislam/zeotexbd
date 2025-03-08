<?php

namespace App\Services;

use App\Models\LoanHolder;
use Illuminate\Support\Facades\DB;

class LoanHolderService
{
    public static function due($id)
    {
        $loanHolder = LoanHolder::select(DB::raw('(-(IFNULL(A.inAmount, 0) - IFNULL(B.outAmount, 0))) AS due')
        )
        ->leftJoin(DB::raw("(SELECT loan_holder_id, SUM(amount) AS inAmount FROM `loans` WHERE type ='Received' GROUP BY loan_holder_id) AS A"), function ($q) {
            $q->on('A.loan_holder_id', '=', 'loan_holders.id');
        })
        ->leftJoin(DB::raw("(SELECT loan_holder_id, SUM(amount) AS outAmount FROM `loans` WHERE type != 'Received' GROUP BY loan_holder_id) AS B"), function ($q) {
            $q->on('B.loan_holder_id', '=', 'loan_holders.id');
        })
        ->where('loan_holders.id', $id)
        ->first();
        if ($loanHolder && $loanHolder->due) {
            return $loanHolder->due;
        }
        return 0;
    }
}