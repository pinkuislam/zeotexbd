<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $fillable = [
        'employee_id', 'degree', 'institution', 'group_subject', 'board_university', 'result', 'passing_year'
    ];
}
