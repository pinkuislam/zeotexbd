<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DyeingPayment extends Model
{
    protected $fillable = [
        'dyeing_agent_id',
        'type',
        'date',
        'receipt_no',
        'total_amount',
        'note',
        'approved_at',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    public function dyeingAgent()
    {
        return $this->belongsTo(DyeingAgent::class);
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }

    public function transactionFor()
    {
        return $this->dyeingAgent();
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
