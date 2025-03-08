<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\User;
use App\Models\Employee;

class BankService
{
    // public static function userBank($user)
    // {
    //     if ($user->type == 'SR') {
    //         $emp = Employee::where('user_id', $user->id)->first();
    //         if ($emp) {
    //             $banks = Bank::select('id', 'bank_name')->whereNotNull('employee_id')->where('employee_id', $emp->id)->where('status', 'Active')->get();
    //         } else {
    //             $banks = [];
    //         }
    //     } else {
    //         $user = User::with(['banks' => function($q) {
    //             $q->select('banks.id', 'banks.bank_name');
    //         }])->find($user->id);
    //         $banks = $user->banks ?? [];
    //     }
    //     return $banks;
    // }

    public static function allBank($user = null)
    {
        $banks = Bank::select('id', 'bank_name')->where('status', 'Active')->get();
        return $banks;
    }

    public static function checkBankAccess($user, $bankId)
    {
        $flag = false;
        $banks = self::userBank($user);
        if ($banks->count() > 0) {
            if (in_array($bankId, $banks->pluck('id')->toArray())) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        return $flag;
    }

    public static function checkTransactionBankAccess($user, $transactions, $type = null)
    {
        $flag = false;
        if ($type == 'Adjustment') {
            $flag = true;
        } else {
            $banks = self::userBank($user);
            if ($banks->count() > 0 && $transactions->count() > 0) {
                foreach ($transactions as $val) {
                    if (in_array($val->bank_id, $banks->pluck('id')->toArray())) {
                        $flag = true;
                    } else {
                        $flag = false;
                        break;
                    }
                }
            }
        }
        return $flag;
    }
}