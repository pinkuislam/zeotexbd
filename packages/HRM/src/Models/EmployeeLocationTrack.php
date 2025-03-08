<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLocationTrack extends Model
{
    protected $fillable = [
        'employee_id', 'address', 'latitude', 'longitude'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
