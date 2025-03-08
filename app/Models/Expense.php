<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    
    protected $guarded  = [];

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
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
