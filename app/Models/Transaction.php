<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'flag', 'flagable_id', 'flagable_type', 'bank_id', 'datetime', 'note', 'amount', 'approved_at', 'approved_by', 'created_by', 'updated_by',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function flagable()
    {
        return $this->morphTo();
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
