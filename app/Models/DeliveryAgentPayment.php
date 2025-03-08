<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgentPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_agent_id',
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

    public function deliveryAgent()
    {
        return $this->belongsTo(DeliveryAgent::class);
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }

    public function transactionFor()
    {
        return $this->supplier();
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
