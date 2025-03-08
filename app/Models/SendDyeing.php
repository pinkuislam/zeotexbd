<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendDyeing extends Model
{
    protected $fillable = [
        'code', 'date', 'dyeing_agent_id', 'created_by', 'updated_by'
    ];

    public function greyItems()
    {
        return $this->morphMany(ProductOut::class, 'flagable');
    }

    public function dyeingAgent()
    {
        return $this->belongsTo(DyeingAgent::class, 'dyeing_agent_id', 'id');
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
