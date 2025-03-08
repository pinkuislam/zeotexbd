<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invest extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id',
        'investor_id',
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
    public function investor()
    {
        return $this->belongsTo(Investor::class);
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
