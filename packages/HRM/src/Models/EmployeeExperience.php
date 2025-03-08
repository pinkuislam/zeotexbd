<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    protected $fillable = [
        'employee_id', 'organization', 'role', 'responsibility', 'joining_date', 'last_working_date', 'duration'
    ];
}
