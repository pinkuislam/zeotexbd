<?php

namespace Oshnisoft\HRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class EmployeeExpenseBill extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'employee_id', 'user_id', 'date', 'daily_summary', 'da_amount', 'ta_amount', 'hotel_bill', 'total_amount', 'is_holiday', 'status', 'approve_at',
        'created_by', 'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
