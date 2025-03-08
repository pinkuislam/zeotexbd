<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DyeingAgent extends Model
{
    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'contact_no',
        'email',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
