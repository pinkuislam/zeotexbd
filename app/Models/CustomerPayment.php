<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'sale_id', 'order_id', 'type', 'date', 'receipt_no', 'amount', 'note', 'approved_at', 'approved_by', 'created_by', 'updated_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function transactionFor()
    {
        return $this->customer();
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
