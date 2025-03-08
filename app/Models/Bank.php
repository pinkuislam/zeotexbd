<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'account_name', 'account_no', 'bank_name', 'branch_name', 'opening_balance', 'status', 'created_by', 'updated_by'
    ];
    public function payments()
    {
        return $this->hasMany(Transaction::class, 'bank_id','id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public static function balance($id = Null){
        $cond = $id != null ? "  AND  payment_id !='".$id."'" : '';
        $data = Bank::select('banks.*',DB::raw("((IFNULL(banks.opening_balance, 0) + IFNULL(A.received, 0)) - (IFNULL(B.payment, 0))) AS balance"))
                ->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS received FROM transactions WHERE type='Received' $cond GROUP BY bank_id) AS A"), function($q) {
                    $q->on('A.bank_id', '=', 'banks.id');
                })
                ->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS payment FROM transactions WHERE type='Payment' $cond GROUP BY bank_id) AS B"), function($q) {
                    $q->on('B.bank_id', '=', 'banks.id');
                })
                ->where([
                        'status'=>'Active'
                    ])
                ->get();
        return $data;
    }
    public static function individualBalance($id,$bank_id){
        $cond = $id != null ? "  AND  payment_id !='".$id."'" : '';
        $data = Bank::select(DB::raw("((IFNULL(banks.opening_balance, 0) + IFNULL(A.received, 0)) - (IFNULL(B.payment, 0))) AS balance"))
                ->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS received FROM transactions WHERE type='Received' $cond GROUP BY bank_id) AS A"), function($q) {
                    $q->on('A.bank_id', '=', 'banks.id');
                })
                ->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS payment FROM transactions WHERE type='Payment' $cond GROUP BY bank_id) AS B"), function($q) {
                    $q->on('B.bank_id', '=', 'banks.id');
                })
                ->where('id',$bank_id)
                ->first();
        return $data;
    }
}
