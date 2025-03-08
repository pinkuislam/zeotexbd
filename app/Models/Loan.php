<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_holder_id', 
        'type', 
        'date', 
        'note', 
        'amount',
        'created_by',
        'updated_by'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function loanHolder()
    {
        return $this->belongsTo(LoanHolder::class);
    }

    public function transactionFor()
    {
        return $this->loanHolder();
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
