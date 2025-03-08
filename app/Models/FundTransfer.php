<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_bank_id', 
        'to_bank_id', 
        'date', 
        'transfer_no', 
        'note', 
        'amount',
        'created_by', 
        'updated_by', 
        'approved_at', 
        'approved_by'
    ];

    public function fromBank()
    {
        return $this->belongsTo(Bank::class, 'from_bank_id');
    }

    public function toBank()
    {
        return $this->belongsTo(Bank::class, 'to_bank_id');
    }

    public function transactionFor()
    {
        return $this->toBank();
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
