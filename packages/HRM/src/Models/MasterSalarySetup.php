<?php

namespace Oshnisoft\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Oshnisoft\HRM\Models\Traits\HasCreatedByAndUpdatedBy;

class MasterSalarySetup extends Model
{
    use HasCreatedByAndUpdatedBy;

    protected $fillable = [
        'basic_salary', 'house_rent', 'medical_allowance', 'conveyance_allowance', 'entertainment_allowance',
        'other_allowance', 'income_tax', 'pf_deduction', 'created_by', 'updated_by'
    ];
}
